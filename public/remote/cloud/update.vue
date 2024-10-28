<template>
  <div class="page-container" v-if="initLock">
    <!-- 应用信息 -->
    <div class="app-table-info">
      <table class="app-table">
        <tbody>
          <tr class="bg-green-tr">
            <td>应用名称</td>
            <td>{{ form?.title }}</td>
            <td>应用版本</td>
            <td>{{ form?.version_name }}</td>
          </tr>
          <tr>
            <td>支持平台</td>
            <td>
              <div class="platform-box">
                <img :src="item?.url" class="logo" v-for="(item, index) in form.platform_icon" :key="index" />
              </div>
            </td>
            <td>应用类型</td>
            <td>{{ form?.plugin_type_text }}</td>
          </tr>
          <tr class="bg-green-tr">
            <td>开发者</td>
            <td>{{ form?.team_title }}</td>
            <td>应用图标</td>
            <td>
              <img :src="form.logo" class="logo" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="submit-button">
      <el-button type="success" class="button" @click="onSubmit">
        开始更新
      </el-button>
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
      user: {},
      form: {
        id: "",
        title: "",
        version: "",
        logo: "",
        platform: "",
        plugin_type: "",
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
    // 提交
    onSubmit() {
      const _this = this;
      _this.$useConfirm('是否确认开始更新？', '温馨提示', 'success').then(() => {
        _this.openWin("remote/cloud/updateing");
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
            _this.form = res?.data ?? {};
            _this.initLock = true;
          } else {
            _this.$useNotify(res?.msg || "获取失败", 'error', '温馨提示', {
              'onClose': () => {
                _this.$emit("update:closeWin");
              }
            })
          }
        })
        .catch((err) => {
          if (err?.code == 11000) {
            _this.openWin("remote/cloud/login");
          }
        });
    },
    getUser() {
      var _this = this;
      _this.$http.useGet("/admin/PluginCloud/index").then((e) => {
          const { data } = e;
          _this.user = data;
          _this.getDetail();
        })
        .catch((err) => {
          if (err?.code == 11000) {
            _this.$emit("openWin", "remote/cloud/login");
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
}

.money {
  color: red;
}</style>
