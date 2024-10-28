<template>
  <div class="page-container" v-if="initLock">
    <!-- 应用信息 -->
    <div class="app-table-info">
      <table class="app-table">
        <tbody>
          <tr class="bg-green-tr">
            <td>应用名称</td>
            <td>{{ findData?.title }}</td>
            <td>应用版本</td>
            <td>{{ findData?.version_name }}</td>
          </tr>
          <tr>
            <td>支持平台</td>
            <td>
              <div class="platform-box">
                <img :src="item?.url" class="logo" v-for="(item, index) in findData.platform_icon" :key="index" />
              </div>
            </td>
            <td>应用类型</td>
            <td>{{ findData?.plugin_type_text }}</td>
          </tr>
          <tr class="bg-green-tr">
            <td>开发者</td>
            <td>{{ findData?.team_title }}</td>
            <td>应用图标</td>
            <td>
              <img :src="findData.logo" class="logo" />
            </td>
          </tr>
          <tr>
            <td>应用售价</td>
            <td>
              <span class="money" v-if="discount > 0 && discount < 1">￥{{ (findData.money * discount).toFixed(2) }}</span>
              <span class="money" v-else>￥{{ findData.money ?? 0 }}</span>
            </td>
            <td>云平台余额</td>
            <td>
              <span class="money">￥{{ user.money ?? 0 }}</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="submit-button">
      <el-button type="success" class="button" @click="onSubmit" v-if="findData?.installed === 'install'">
        开始安装
      </el-button>
      <div v-else-if="findData?.installed == '' && findData?.updateed == ''">
        <div class="flex mb-4">
          <div class="flex-1" v-if="Coupon.length > 0">
            <vxe-select v-model="form.coupon_code" placeholder="选择优惠券" class="coupon-select" @change="CouponSelected">
              <vxe-option v-for="(item, index) in Coupon" :key="index" :value="item.coupon_code" :label="item.title">
                <div class="flex">
                  <div class="flex-1">{{ item.title }}</div>
                  <div>{{ item.discount * 10 }}折</div>
                </div>
              </vxe-option>
            </vxe-select>
          </div>
          <el-button type="primary" class="button flex-2" @click="onBuy">
            确认购买
          </el-button>
        </div>
        <el-alert title="购买须知" type="warning" :closable="false">
          购买后将绑定当前站点：{{ host }}，将仅限当前站点安装、更新、卸载
        </el-alert>
      </div>
    </div>
    <div class="app-desc-container">
      <div class="app-desc-title">
        <div class="desc-title">更新日志</div>
        <div class="more-title" @click="hanldMore">历史日志</div>
      </div>
      <el-tag type="info" class="app-desc">
        {{ form?.dev_version?.remarks ? form.dev_version.remarks : "暂无更多日志" }}
      </el-tag>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    ajaxParams: {
      type: Object,
      default: () => {
        return {};
      },
    },
  },
  data() {
    return {
      // 页面初始化
      initLock: false,
      host: window.location.host,
      user: {
        money: 0
      },
      discount: 0,
      Coupon: [],
      form: {
        name: '',
        version: '',
        coupon_code: '',
      },
      findData: {
        id: "",
        title: "",
        version: "",
        logo: "",
        platform: "",
        plugin_type: "",
        coupon_code: "",
        money: 0,
        author: {
          title: "",
        },
        dev_version: {
          remarks:'',
        }
      },
    };
  },
  created() {
    this.getUser();
  },
  methods: {
    // 发送购买请求
    sendBuy() {
      const _this = this;
      _this.$http.usePost("admin/Plugin/buy", this.form).then((res) => {
        _this.$emit("update:openWin", "remote/cloud/install");
        _this.$useNotify(res?.msg || '操作成功', 'success', '温馨提示');
      }).catch((err) => {
        _this.$useNotify.useNotify(err?.msg || '异常错误', 'error', '温馨提示');
      })
    },
    // 购买
    onBuy() {
      const _this = this;
      _this.$useConfirm('是否确认购买该应用？', '温馨提示', 'warning').then(() => {
        _this.sendBuy();
      })
    },
    // 提交
    onSubmit() {
      const _this = this;
      _this.$useConfirm('是否确认开始安装？', '温馨提示', 'success').then(() => {
        _this.openWin("remote/cloud/installing");
      })
    },
    openWin(path) {
      this.$emit("update:openWin", path);
    },
    hanldMore() {
      const url = `https://www.xhadmin.cn/#/apps/detail?name=${this.form.name}&action=version`;
      window.open(url)
    },
    getDetail() {
      const _this = this;
      const queryParams = {
        ...this.ajaxParams,
      };
      _this.$http
        .useGet("admin/Plugin/detail", queryParams)
        .then((res) => {
          if (res.code === 200) {
            this.findData = res?.data;
            this.form.name = res?.data?.name;
            this.form.version = res?.data?.version;
            this.getAvailableCoupon(res?.data?.id);
            _this.initLock = true;
          } else {
            _this.$useNotify(res?.msg || "操作失败", 'error', '温馨提示', {
              'onClose': () => {
                _this.$emit("update:closeWin");
              }
            })
          }
        })
        .catch((err) => {
          if ([11000, 666, 600].includes(err?.code)) {
            _this.$emit("update:openWin", "remote/cloud/login");
          }
        });
    },
    getAvailableCoupon(id) {
      this.$http
        .useGet("admin/PluginCloud/getAvailableCoupon", { type: 'apps', plugin_id: id })
        .then((res) => {
          if (res.code === 200) {
            this.Coupon = res?.data;
            if (this.Coupon.length > 0) {
              this.CouponSelected({ value: this.Coupon[0].coupon_code });
            }
          }
        });
    },
    CouponSelected({ value: coupon_code }) {
      this.form.coupon_code = coupon_code;
      const find = this.Coupon.find((item) => item.coupon_code == coupon_code);
      if (find) {
        this.discount = find.discount;
      }
    },
    getUser() {
      var _this = this;
      _this.$http
        .useGet("/admin/PluginCloud/index")
        .then((e) => {
          const { data } = e;
          _this.user = data;
          _this.getDetail();
        })
        .catch((err) => {
          if ([11000, 666, 600].includes(err?.code)) {
            _this.$emit("update:openWin", "remote/cloud/login");
          }
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.page-container {
  overflow-y: auto;
  overflow-x: hidden;
  height: 100%;

  .app-table-info {
    padding: 20px 20px 0 20px;

    .app-table {
      width: 100%;
      border: 1px solid #e5e5e5;
      border-radius: 3px;
      text-align: left;
      border-collapse: separate;
      border-spacing: 0;
      overflow: hidden;

      tr {
        height: 45px;

        td {
          padding: 0 10px;
        }
      }

      .bg-green-tr {
        background: var(--el-color-primary);
        color: #fff;
      }
    }
  }


  .submit-button {
    margin-top: 30px;
    padding: 0 20px;

    .button {
      width: 100%;
      height: 45px;
    }
  }

  .app-desc-container {
    margin-top: 20px;
    padding: 20px;

    .app-desc-title {
      display: flex;
      justify-content: space-between;

      .desc-title {
        font-size: 25px;
        font-weight: 700;
      }

      .more-title {
        display: flex;
        align-items: center;
        cursor: pointer;
        &:hover{
          color:#409eff;
        }
      }
    }

    .app-desc {
      width: 100%;
      height: 230px;
      display: block;
      padding: 10px;
      margin-top: 10px;
      white-space: pre-line;
      line-height: 25px;
      overflow-y: auto;
      overflow-x: hidden;
    }
  }
}

.platform-box {
  display: flex;
  gap: 12px;
}

.logo {
  width: 36px;
  height: 36px;
  border-radius: 5px;
}

.money {
  color: red;
}

.mb-4 {
  margin-bottom: 20px;
}

.flex {
  display: flex;
  align-items: center;
}

.flex-1 {
  flex: 1;
}

.flex-2 {
  flex: 2;
}

.coupon-select {
  width: 95%;

  .vxe-input {
    height: 45px;
    line-height: 45px;
  }
}
</style>