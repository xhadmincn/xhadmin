<template>
  <div class="update-container" v-if="updated.version">
    <div class="title">本地当前版本 {{ updated.client_version_name }}（{{ updated.client_version }}）</div>
    <div class="content">
      <div class="main-tabs">
        <div class="item" :class="{ active: stepData.step === item?.step }" v-for="(item, index) in stepData.list"
          :key="index">
          <div class="step">{{ index + 1 }}</div>
          <div class="text">{{ item.title }}</div>
        </div>
      </div>
      <!-- 更新中 -->
      <div class="update-desc-container" v-if="stepData.lock">
        <div class="update-desc-title">
          <div class="next-title">更新中，请勿刷新或离开当前页面...</div>
        </div>
        <div class="update-ing">
          <AppIcons class="update-loading" icon="Refresh" />
          <span class="update-loading-text">{{ stepData?.updateText }}</span>
        </div>
      </div>
      <!-- 准备更新 -->
      <div class="update-desc-container" v-else>
        <div class="update-desc-title">
          <div class="update-desc-version">
              发现新版本更新 {{ updated.version_name }}（{{ updated.version }}）
          </div>
          <div class="update-desc-time">
            发布时间：{{ updated.create_at }}
          </div>
        </div>
        <pre class="update-desc">{{ updated?.content }}</pre>
        <div class="update-buttons">
          <button class="update-btn submit-btn" @click="hanldUpdate">
            立即更新
          </button>
        </div>
      </div>
      <!-- 温馨提示 -->
      <div class="update-tip">
        <el-alert title="更新小提示" type="error" :closable="false">
          <div class="update-line-content">
            <div>1、无论更新成功与否，都会在更新前备份代码与数据库，可放心更新</div>
            <div>2、备份的代码与数据库会在站点根目录下的backup目录下</div>
            <div>3、切记，backup目录下的备份文件，相同版本备份文件会覆盖</div>
            <div>4、更新过程中，会出现更新中的提示，不要刷新页面或离开当前页面</div>
            <div>5、更新前，请确保站点根目录下的.env文件里的APP_DEBUG为false</div>
          </div>
        </el-alert>
      </div>
    </div>
  </div>
</template>
  
<script>
export default {
  data() {
    return {
      stepData: {
        step: 'preparation',
        lock: false,
        updateText: '',
        list: [
          {
            title: '准备更新',
            step: 'preparation',
          },
          {
            title: '下载更新包',
            step: 'download',
          },
          {
            title: '备份代码',
            step: 'backCode',
          },
          {
            title: '备份数据库',
            step: 'backSql',
          },
          {
            title: '解压更新包',
            step: 'unzip',
          },
          {
            title: '更新数据',
            step: 'updateData',
          },
          {
            title: '更新成功',
            step: 'success',
          },
        ],
      },
      updated: {
        update: true,
        title: "",
        version_name: "",
        version: "",
        client_version_name: "",
        client_version: "",
        content: "",
      },
    }
  },
  methods: {
    // 更新进行中
    hanldStepUpdate(step) {
      const _this = this;
      const version = _this.updated.version;
      // 设置开始更新与文字
      const item = _this.stepData.list.find(item => item.step === step);
      _this.stepData.updateText = `正在${item.title}...`;
      // 设置当前步骤
      _this.stepData.step = step
      // 发送更新请求
      _this.$http.usePost(`admin/Updated/updateCheck?step=${step}&version=${version}`).then((res) => {
        if (res?.data?.next === '') {
          _this.stepData.step = 'success';
          _this.stepData.updateText = res.msg;
          setTimeout(() => {
            _this.$routerApp.push({ path: '/' });
          }, 2500);
        } else if (res?.data?.next) {
          _this.hanldStepUpdate(res.data.next);
        }
      }).catch((err) => {
        _this.stepData.step = 'preparation';
        _this.stepData.lock = false;
        if (err?.message?.includes('timeout')) {
          _this.$useNotify("更新失败，网络超时", 'error', '温馨提示', {
            'onClose': () => {
              window.location.reload();
            }
          })
          return;
        }
      });
    },
    // 点击更新
    hanldUpdate() {
      const _this = this;
      _this.$useConfirm('是否确定现在开始更新系统框架？', '温馨提示', 'success').then(() => {
        _this.stepData.lock = true;
        _this.hanldStepUpdate('download');
      })
    },
    // 获取版本详情
    getDetail() {
      const _this = this;
      _this.$http
        .usePut("admin/Updated/updateCheck")
        .then((res) => {
          const { data } = res;
          _this.updated = data;
        }).catch((err) => {
          if ([600, 666].includes(err?.code)) {
            _this.updated.client_version = err?.data?.client_version
            _this.updated.client_version_name = err?.data?.client_version_name
          }
        })
    },
  },
  mounted() {
    this.getDetail()
  },
}
</script>
  
<style lang="scss">
.update-container {
  height: 100%;
  background: #fff;
  display: flex;
  flex-direction: column;

  .title {
    padding: 20px 30px;
    font-size: 18px;
  }

  .content {
    flex: 1;
    margin: 20px 0;
    padding: 0 30px;
    overflow-y: auto;
    overflow-x: hidden;

    .main-tabs {
      display: flex;
      border-bottom: 1px solid #e5e5e5;

      .item {
        width: 180px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 10px;
        padding: 15px 0;
        user-select: none;

        .step {
          width: 40px;
          height: 40px;
          background: #ccc;
          border-radius: 50%;
          display: flex;
          justify-content: center;
          align-items: center;
          color: #fff;
          font-weight: 700;
        }

        .text {
          font-weight: 700;
          font-size: 16px;
        }
      }

      .active {
        border-bottom: 5px solid #006EFF;

        .step {
          background: #006EFF;
        }
      }
    }

    .update-desc-container {
      padding: 20px 0;

      .update-desc-title {
        font-size: 20px;
        font-weight: 700;
        display: flex;
        justify-content: space-between;
        .update-desc-version{

        }
        .update-desc-time{}
        .next-title{
          color:red;
        }
      }
      .update-ing{
        display: flex;
        align-items: center;
        font-size: 14px;
        line-height: 26px;
        color: #70767E;
        white-space: pre-wrap;
        word-break: break-word;
        padding: 20px 0;
        margin:0;
        .update-loading{
          animation: update-ing 1s linear infinite;
        }
        .update-loading-text{
          padding-left:5px;
        }
      }

      .update-desc {
        height: 200px;
        font-size: 14px;
        line-height: 26px;
        color: #70767E;
        white-space: pre-wrap;
        word-break: break-word;
        padding: 10px 0;
        margin: 10px 0;
        overflow-x: hidden;
        overflow-y: auto;
        border-bottom: 1px solid #e5e5e5;
      }

      .update-buttons {
        display: flex;
        gap: 30px;

        .update-btn {
          width: 150px;
          height: 40px;
          border-radius: 4px;
          border: 0;
          cursor: pointer;
        }

        .submit-btn {
          color: #fff;
          background: #016EFF;
          transition: all 0.1s;

          &:hover {
            background: #61a6ff;
          }
        }

        .rest-btn {
          background: #fff;
          color: #016EFF;
          border: 1px solid #e1e1e1;
          transition: all 0.1s;

          &:hover {
            background: #f9f9f9;
          }
        }
      }
    }

    .update-tip {
      margin-top: 10px;
    }
  }
}

.update-line-content {
  line-height: 36px;
  font-size: 16px;
}
@keyframes update-ing {
 from {
  -webkit-animation: rotate(0deg);
    transform: rotate(0deg);
 }
 to {
  -webkit-animation: rotate(360deg);
   transform: rotate(360deg);
 }
}
</style>
  