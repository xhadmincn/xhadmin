<template>
  <div class="update-version-container" v-if="updated.client_version">
    <div class="version-info">
      <div class="xhadmin-version">
        <el-image class="system-logo" src="/image/logo.png" />
        <div class="system-name">
          {{ system_info?.system_name }}
        </div>
        <div class="system-version">
          版本 {{ updated.client_version_name }}（{{ updated.client_version }}）
        </div>
      </div>
      <div class="xhadmin-empty">
        <el-empty image="/image/complete.png" description="已经是最新版本"></el-empty>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      updated: {
        update: true,
        title: "",
        version_name: "",
        version: "",
        client_version_name: "",
        client_version: "",
        content: "",
      },
      system_info: {},
    }
  },
  methods: {
    getDetail() {
      const _this = this;
      _this.$http
        .usePut("admin/Updated/updateCheck")
        .then((res) => {
          const { data } = res;
          data.update = data.version > data.client_version;
          _this.updated = data;
          if (data.update) {
            _this.$emit('update:openWin', 'remote/update/update')
          }
        }).catch((err) => {
          if ([600, 666].includes(err?.code)) {
            _this.updated.client_version = err?.data?.client_version
            _this.updated.client_version_name = err?.data?.client_version_name
          }
        })
    },
    getEmpower() {
      const _this = this;
      _this.$http.usePut("admin/Updated/empower").then((res) => {
        const { data } = res;
        _this.system_info = data?.system_info || {};
      })
    }
  },
  mounted() {
    this.getDetail()
    this.getEmpower()
  },
}
</script>

<style lang="scss" scoped>
.update-version-container {
  height: 100%;
  background: #fff;

  .version-info {
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    .xhadmin-version {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;

      .system-logo {
        width: 120px;
        height: 120px;
        border-radius: 10px;
      }

      .system-name {
        padding-top: 20px;
        font-weight: 700;
        font-size: 26px;
      }

      .system-version {
        padding-top: 10px;
        font-size: 14px;
      }
    }
  }
}
</style>