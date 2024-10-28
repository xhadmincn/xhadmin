<template>
  <div class="app-container">
    <!-- 筛选面板 -->
    <div class="xh-search-card">
      <div class="tabs">
        <div class="item" :class="{ active: '' === category.active }" @click="hanldCategory('')">
          全部
        </div>
        <div class="item" :class="{ active: item?.id === category.active }" v-for="(item, index) in category.list"
          :key="index" @click="hanldCategory(item?.id)">
          {{ item.title }}
        </div>
      </div>
      <div class="search">
        <el-form :inline="true" class="demo-form-inline">
          <el-form-item>
            <el-select placeholder="请选择应用类型" @change="hanldPlatform" v-model="platforms.active">
              <el-option label="全部" value=""></el-option>
              <el-option :label="item.label" :value="item.value" v-for="(item, index) in platforms.list" :key="index" />
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-select placeholder="请选择安装状态" @change="hanldInstalled" v-model="installed.active">
              <el-option label="全部" value=""></el-option>
              <el-option :label="item.label" :value="item.value" v-for="(item, index) in installed.list" :key="index" />
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-input placeholder="请输入应用名称" v-model="keyword"></el-input>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click="onSubmit">
              <template #icon>
                <AppIcons icon="Search" :size="15" />
              </template>
              搜索
            </el-button>
          </el-form-item>
        </el-form>
      </div>
    </div>
    <!-- 应用列表 -->
    <div class="xh-apps-list" v-if="plugins.length">
      <el-row :gutter="20">
        <el-col class="xh-row" :xs="24" :sm="12" :md="8" :lg="6" :xl="4" v-for="(item, index) in plugins" :key="index" @click="hanldDetail(item)">
          <el-card class="box-card">
            <div slot="header" class="clearfix">
              <el-image style="width: 100%; height: 190px;border-radius: 3px;" :src="item.logo" />
            </div>
            <div class="new-update" v-if="item.updateed === 'update'">
              <AppIcons icon="Bell" :size="13" type="element" />
              <span class="text">有新版本</span>
            </div>
            <div class="content">
              <div class="title">
                <el-tag type="primary" v-if="item?.platform_text?.length">
                  {{ item.platform_text[0] }}
                </el-tag>
                <div class="text">{{ item.title }}</div>
              </div>
              <div class="foot">
                <div class="i">
                  <div class="icon">
                    <AppIcons color="#909399" type="element" icon="Download" :size="14" />
                  </div>
                  <div class="number">{{ item.down }}</div>
                </div>
                <div class="i">
                  <div class="icon">
                    <AppIcons color="#909399" type="element" icon="View" :size="14" />
                  </div>
                  <div class="number">{{ item.view }}</div>
                </div>
                <div class="i">
                  <!-- 购买/安装 -->
                  <div class="price" v-if="item.installed === 'install'">￥{{ item.money }}</div>
                  <!-- 可更新 -->
                  <el-button size="small" type="success" v-else-if="item.updateed === 'update'">
                    有更新
                  </el-button>
                  <!-- 无操作 -->
                  <el-button size="small" type="info" v-else>
                    已安装
                  </el-button>
                </div>
              </div>
            </div>
          </el-card>
        </el-col>
      </el-row>
    </div>
    <div class="project-empty" v-else>
      <el-empty description="没有更多的应用" />
    </div>
    <!-- 分页 -->
    <div class="pagination" v-if="plugins.length">
      <el-pagination background layout="prev, pager, next" :total="pagination.total" :page-size="pagination.limit"
        :current-page="pagination.page" @current-change="setPage" />
    </div>
    <!-- 应用详情弹窗 -->
    <div class="xh-apps-dialog" v-if="dialogData.show">
      <el-dialog model-value="true" title="应用详情" width="850px" top="5%" :show-close="true" :close-on-click-modal="false"
        :before-close="handleClose">
        <div class="apps">
          <div class="left">
            <div class="apps-info">
              <div class="banner">
                <el-carousel indicator-position="outside" height="240px">
                  <el-carousel-item v-for="item in detail?.thumb" :key="item">
                    <el-image style="width: 280px; height: 280px;border-radius: 3px;" :src="item?.url" />
                  </el-carousel-item>
                </el-carousel>
              </div>
              <div class="detail">
                <div class="items title">
                  <el-tag type="primary" v-if="detail?.platform_text?.length">
                    {{ detail?.platform_text[0] }}
                  </el-tag>
                  <div class="text">{{ detail?.title }}</div>
                </div>
                <div class="items">
                  <div class="label">发布时间：</div>
                  <div class="value">{{ detail?.create_at }}</div>
                </div>
                <div class="items">
                  <div class="label">应用价格：</div>
                  <div class="value price">￥{{ detail?.money }}</div>
                </div>
                <div class="items">
                  <div class="label">最新版本：</div>
                  <div class="value">{{ detail?.version_name }}</div>
                </div>
                <div class="items" v-if="detail?.localVersion > 999">
                  <div class="label">当前版本：</div>
                  <div class="value">已安装 {{ detail?.localVersionName }} 版本</div>
                </div>
                <div class="items">
                  <div class="label">应用分类：</div>
                  <div class="value">
                    <el-tag>{{ detail?.cate_title }}</el-tag>
                  </div>
                </div>
                <div class="items" v-if="!detail?.installed">
                  <div class="label">授权数量：</div>
                  <div class="value">
                    <el-tag type="danger">{{ detail?.auth_num }}个授权</el-tag>
                  </div>
                </div>
                <div class="items" v-else>
                  <div class="label">拥有授权：</div>
                  <div class="value">
                    <el-tag type="success">{{ detail?.have_auth_num }}个授权</el-tag>
                    <el-button type="success" link @click="hanldBindSite">
                      <template #icon>
                        <AppIcons icon="Promotion" />
                      </template>
                      获取更多授权
                    </el-button>
                  </div>
                </div>
                <div class="items">
                  <div class="label">下载次数：</div>
                  <div class="value">{{ detail?.down }}次</div>
                </div>
                <div class="items">
                  <el-row class="apps-button">
                    <el-button type="info" v-if="detail?.bindsite" @click="hanldBindSite">
                      (剩余{{ detail?.bindsite }}个) 去绑定
                    </el-button>
                    <el-button type="warning" v-if="detail?.installed === '' && detail?.updateed === ''" @click="onBuy">
                      <template #icon>
                        <AppIcons icon="Present" />
                      </template>
                      购买
                    </el-button>
                    <el-button type="primary" v-if="detail?.installed === 'install'" @click="toInstall">
                      <template #icon>
                        <AppIcons icon="Lightning" />
                      </template>
                      安装
                    </el-button>
                    <el-button type="success" v-if="detail?.updateed === 'update'" @click="toUpdate">
                      <template #icon>
                        <AppIcons icon="Compass" />
                      </template>
                      更新
                    </el-button>
                    <el-button type="danger" v-if="detail?.installed === 'uninstall'" @click="toUnInstall">
                      <template #icon>
                        <AppIcons icon="Delete" />
                      </template>
                      卸载
                    </el-button>
                  </el-row>
                </div>
              </div>
            </div>
            <div class="alert">
              <el-alert :closable="false" title="购买应用和当前域名、IP、云账户关联，不支持更换！" type="error">
              </el-alert>
            </div>
            <div class="apps-card">
              <div class="title">
                应用简介
              </div>
              <div class="content">
                <pre class="pre-line" v-html="detail?.desc"></pre>
              </div>
            </div>
            <div class="apps-card">
              <div class="title">
                更新日志
              </div>
              <div class="content">
                <el-timeline v-if="detail?.version_log?.length">
                  <el-timeline-item icon="MoreFilled" type="primary" color="#0bbd87" size="large"
                    :timestamp="item.create_at" v-for="(item, index) in detail?.version_log" :key="index">
                    <div class="version-info">{{ item?.version_name }}（{{ item?.version }}）</div>
                    <pre class="pre-line">{{ item?.remarks }}</pre>
                  </el-timeline-item>
                </el-timeline>
              </div>
            </div>
          </div>
          <div class="right">
            <div class="user">
              <div class="avatar">
                <el-avatar :size="51" :src="user?.headimg" />
              </div>
              <div class="info">
                <div class="nickname">{{ user?.nickname ?? '暂无昵称' }}</div>
                <div class="date">余额：{{ user?.money }}</div>
              </div>
            </div>
            <div class="bill">
              <div class="header">
                <div class="title">账单流水</div>
                <!-- <div class="more">
                  <el-text type="primary">查看更多</el-text>
                </div> -->
              </div>
              <div class="record" ref="billListRef" v-if="bill.length">
                <div class="item" v-for="(item, index) in bill" :key="index">
                  <div class="content">
                    <div class="text">
                      <div>{{ item?.remarks }}</div>
                      <div class="price" v-if="item?.bill_type === 0">-￥{{ item?.money }}</div>
                      <div class="price" v-if="item?.bill_type === 1">+￥{{ item?.money }}</div>
                    </div>
                    <div class="date">{{ item?.create_at }}</div>
                  </div>
                </div>
              </div>
              <el-empty v-else description="暂无更多账单" />
            </div>
          </div>
        </div>
      </el-dialog>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      // 分类选项
      category: {
        active: '',
        list: []
      },
      // 平台类型选项
      platforms: {
        active: '',
        list: []
      },
      // 是否安装选项
      installed: {
        active: '',
        list: []
      },
      // 搜索筛选
      keyword: '',
      // 分页数据
      pagination: {
        page: 1,
        limit: 20,
        total: 0,
      },
      // 应用列表
      plugins: [],
      // 用户信息
      user: {},
      // 应用详情
      detail: {},
      // 账单列表
      bill: [],
      // 弹窗信息
      dialogData: {
        show: false,
      },
    };
  },
  methods: {
    // 去绑定站点
    hanldBindSite() {
      const _this = this;
      const loading = _this.$useLoading('打开中...', {
        background: 'rgba(0, 0, 0, 0.7)',
      });
      const queryParams = {
        type: 'bindsite'
      }
      _this.$http.useGet("admin/Plugin/getLink", queryParams).then((res) => {
        if (!res?.data?.url) {
          _this.$useNotify('未能成功获取跳转连接', 'error', '温馨提示');
          return;
        }
        window.open(res?.data?.url);
      }).catch((err) => {
        _this.$useNotify(err?.msg || '异常错误', 'error', '温馨提示');
      }).finally(() => {
        loading.close();
      })
    },
    // 卸载应用
    toUnInstall() {
      const _this = this;
      _this.$useConfirm('是否确认卸载该应用？', '温馨提示', 'error').then(() => {
        const loading = _this.$useLoading('正在卸载中...', {
          background: 'rgba(0, 0, 0, 0.7)',
        });
        const queryParams = {
          name: _this.detail?.name,
          version: _this.detail?.version
        };
        _this.$http.usePost("admin/Plugin/uninstall", queryParams).then((res) => {
          _this.$useNotify(res?.msg || '操作成功', 'success', '温馨提示');
          _this.dialogData.show = false;
          _this.getList();
        }).finally(() => {
          loading.close();
        })
      })
    },
    // 跳转更新
    toUpdate() {
      const _this = this;
      _this.$useConfirm('是否确认开始更新？', '温馨提示', 'success').then(() => {
        const queryParams = {
          ajaxParams: {
            name: _this.detail?.name,
            version: _this.detail?.version
          },
        };
        _this.dialogData.show = false;
        _this.$useRemote('remote/cloud/updateing', queryParams)
      })
    },
    // 跳转安装
    toInstall() {
      const _this = this;
      _this.$useConfirm('是否确认开始安装？', '温馨提示', 'warning').then(() => {
        const queryParams = {
          ajaxParams: {
            name: _this.detail?.name,
            version: _this.detail?.version
          },
        };
        _this.dialogData.show = false;
        _this.$useRemote('remote/cloud/installing', queryParams)
      })
    },
    // 发送购买请求
    sendBuy() {
      const _this = this;
      const queryParams = {
        name: _this.detail?.name,
        version: _this.detail?.version
      }
      _this.$http.usePost("admin/Plugin/buy", queryParams).then((res) => {
        _this.$useNotify(res?.msg || '操作成功', 'success', '温馨提示');
        _this.getList();
        _this.dialogData.show = false;
      }).catch((err) => {
        _this.$useNotify(err?.msg || '异常错误', 'error', '温馨提示');
      })
    },
    // 购买
    onBuy() {
      const _this = this;
      _this.$useConfirm('是否确认购买该应用？', '温馨提示', 'warning').then(() => {
        _this.sendBuy();
      })
    },
    // 搜索
    onSubmit() {
      this.getList();
    },
    // 关闭弹窗
    handleClose() {
      this.dialogData.show = false;
      this.getList();
    },
    // 获取用户信息
    async getUser(e) {
      var _this = this;
      return await _this.$http.useGet("/admin/PluginCloud/index").then((res) => {
        _this.user = res?.data;
      })
    },
    // 获取应用详情
    async getDetail(e) {
      const _this = this;
      _this.detail = {};
      const queryParams = {
        name: e?.name,
        version: e?.version
      };
      return await _this.$http.useGet("admin/Plugin/detail", queryParams).then((res) => {
        _this.detail = res?.data ?? {};
        _this.dialogData.show = true;
      })
    },
    async getBill() {
      var _this = this;
      return await _this.$http.useGet("/admin/PluginCloud/bill").then((e) => {
        const { data } = e;
        _this.page++;
        const pageData = data.data;
        for (let index = 0; index < pageData.length; index++) {
          const element = pageData[index];
          _this.bill.push(element);
        }
        // _this.$nextTick(()=>{
        //   if(_this.$refs.billListRef.scrollHeight <= _this.$refs.billListRef.clientHeight){
        //     setTimeout(() => {
        //       _this.getBill();
        //     }, 300);
        //   }
        // })
      })
    },
    // 打开详情弹窗
    async hanldDetail(e) {
      await this.getUser(e);
      await this.getBill();
      await this.getDetail(e);
    },
    // 打开页面
    openWin(path) {
      this.$emit("update:openWin", path);
    },
    // 选择平台
    hanldPlatform(e) {
      this.platforms.active = e;
      this.getList();
    },
    // 选择安装状态
    hanldInstalled(e) {
      this.installed.active = e;
      this.getList();
    },
    // 选择分类
    hanldCategory(e) {
      this.category.active = e;
      this.getList();
    },
    // 获取插件其他数据
    getPluginData() {
      const _this = this;
      _this.$http.usePut("admin/Plugin/getPluginData").then((res) => {
        _this.category.list = res?.data?.category ?? [];
        _this.platforms.list = res?.data?.platforms ?? [];
        _this.installed.list = res?.data?.installed ?? [];
      })
    },
    setPage(page) {
      this.pagination.page = page;
      this.getList();
    },
    // 获取列表
    getList() {
      const _this = this;
      const params = {
        page: _this.pagination.page,
        limit: _this.pagination.limit,
        category: _this.category.active,
        platform: _this.platforms.active,
        installed: _this.installed.active,
        keyword: _this.keyword
      }
      _this.$http.useGet("admin/Plugin/index", params).then((res) => {
        _this.pagination.total = res?.data?.total ?? 0;
        _this.pagination.page = res?.data?.current_page ?? 1;
        _this.pagination.limit = res?.data?.per_page ?? 20;
        _this.plugins = res.data.data;
      })
    },
    // 初始化数据
    async initify() {
      await this.getPluginData();
      await this.getList();
    }
  },
  mounted() {
    this.initify();
  },
};
</script>

<style lang="scss" scoped>
.app-container {
  height: 100%;
  display: flex;
  flex-direction: column;
}

.xh-apps-dialog {
  .el-dialog__body {
    padding-top: 0px;
    padding-bottom: 0px;
    height: 650px;
    overflow-y: auto;
    overflow-x: hidden;
  }

  .el-dialog__footer {
    padding: 0px;
  }

  .apps {
    display: flex;
    flex-direction: row;
    justify-content: space-between;

    .right {
      width: 25%;
      border-left: 1px solid #F5F7FA;
      padding-left: 15px;

      .bill {
        background-color: #FAFAFA;
        padding: 15px;
        border-radius: 5px;

        .record {
          display: flex;
          flex-direction: column;

          .content {

            width: 100%;
          }

          .item {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            width: 100%;

            .text {
              width: 100%;
              font-weight: 600;
              margin-bottom: 5px;
              font-size: 12px;
              display: flex;
              flex-direction: row;
              justify-content: space-between;

              .price {
                color: #F56C6C;
              }
            }

            .date {
              font-size: 12px;
              color: #606266;
            }
          }
        }

        .header {
          display: flex;
          flex-direction: row;
          justify-content: space-between;
          font-size: 14px;
          font-weight: bolder;
          margin-bottom: 15px;
          border-bottom: 1px solid #F0F2F5;
          padding-bottom: 15px;

          .title {
            font-weight: bolder;
          }

          .more {
            cursor: pointer;
          }
        }
      }

      .user {
        display: flex;
        flex-direction: row;
        align-items: center;
        border-bottom: 1px solid #F5F7FA;
        padding-bottom: 15px;
        margin-bottom: 10px;

        .avatar {
          margin-right: 10px;
        }

        .info {
          .nickname {
            font-size: 16px;
            font-weight: bolder;
          }

          .date {
            color: #909399;
            font-size: 12px;
          }
        }
      }
    }

    .left {
      width: 75%;

      .alert {
        border-top: 1px solid #F5F7FA;
        margin: 15px 0px;
        padding-top: 15px;
        width: 95%;
      }

      .apps-card {
        margin-right: 15px;
        border-top: 1px solid #F5F7FA;

        .title {
          font-size: 16px;
          font-weight: bolder;
          margin-top: 15px;
        }

        .content {
          padding: 15px;

          .version-info {
            font-size: 14px;
          }
        }
      }

      .apps-info {
        width: 100%;
        display: flex;
        flex-direction: row;

        .banner {
          width: 50%;
        }

        .detail {
          width: 50%;

          .items {
            display: flex;
            flex-direction: row;
            align-items: center;
            margin-bottom: 10px;

            .label {
              color: #909399;
            }

            .value {
              color: #606266;
            }

            .price {
              color: #f56c6c;
              font-size: 16px;
              font-weight: bolder;
            }
          }

          .apps-button {
            margin-top: 5px;
          }

          .title {
            .text {
              margin-left: 5px;
              font-weight: bold;
            }
          }
        }
      }
    }
  }
}

.pagination {
  background-color: #fff;
  padding: 20px;
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: flex-end;
}

.project-empty {
  background-color: #fff;
  padding: 50px 0;
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: center;
}

.xh-apps-list {
  background-color: #fff;
  padding: 20px;
  margin-top: 10px;
  padding-bottom: 30px;
  border-radius: 3px;
  flex: 1;
  overflow: auto;

  .xh-row {
    margin-bottom: 15px;
  }

  .box-card {
    transition: all .3s ease;
    cursor: pointer;
    position: relative;

    .new-update {
      position: absolute;
      top: 0;
      right: 0;
      background-color: #f56c6c;
      font-size: 8px;
      color: #fff;
      padding: 5px 8px;
      border-radius: 0px 5px 0px 10px;
      z-index: 999;
      display: flex;
      align-items: center;

      .text {
        padding-left: 5px;
      }
    }

    .content {
      .foot {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        margin-top: 10px;

        .i {
          display: flex;
          flex-direction: row;
          align-items: center;

          .number {
            font-size: 14px;
            color: #909399;
            margin-left: 5px;
          }

          .price {
            color: #f56c6c;
            font-size: 16px;
            font-weight: bolder;
          }

          .already {
            font-size: 14px;
            color: #606266;
          }
        }
      }

      .title {
        display: flex;
        flex-direction: row;
        align-items: center;

        .text {
          margin-left: 5px;
          font-size: 14px;
          flex: 1;
          overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;
        }
      }
    }
  }

  .box-card:hover {
    -webkit-transform: translateY(-4px) scale(1.02);
    -moz-transform: translateY(-4px) scale(1.02);
    -ms-transform: translateY(-4px) scale(1.02);
    -o-transform: translateY(-4px) scale(1.02);
    transform: translateY(-4px) scale(1.02);
    -webkit-box-shadow: 0 14px 24px rgba(0, 0, 0, .2);
    box-shadow: 0 14px 24px #0003;
    z-index: 999;
    border-radius: 6px
  }
}

.xh-search-card {
  background-color: #fff;
  padding: 10px;
  border-radius: 3px;
  display: flex;
  flex-direction: row;
  align-items: center;
  font-size: 14px;

  .tabs {
    display: flex;
    flex-direction: row;
	flex:1;

    .item {
      padding: 10px;
      cursor: pointer;
    }

    .active {
      color: #0d84ff;
    }
  }

  .search {

    .el-form-item {
      .el-select{
        --el-select-width: 100px;
      }
      margin-bottom: 0px !important;
    }
  }
}

.pre-line {
  white-space: pre-wrap;
  word-wrap: break-word;
  word-break: break-all;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 10;
  font-size: 14px;
  color: #606266;
}
@media only screen and (max-width: 1500px){
	.xh-search-card {
		flex-direction:column-reverse;
		flex-wrap: wrap;
    align-items: flex-start;
		.tabs {
			width:100%;
		}
	}
}
</style>