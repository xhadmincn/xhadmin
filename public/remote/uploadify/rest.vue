<template>
    <div class="h-100% app-container">
        <el-button type="primary" @click="hanldRest">重置所有上传设置</el-button>
    </div>
</template>
  
<script>
export default {
    props: {
        modelValue: Object,
    },
    data() {
        return {
        }
    },
    mounted() {
    },
    methods: {
        sendRest() {
            const _this = this;
            let path = this.$routeApp.path
            // 删除最后一个斜杠之后内容
            path = path.substring(0, path.lastIndexOf("/"))
            const api = this.$moduleName + path + '/rest'
            _this.$http.useGet(api).then((res) => {
                _this.$useNotify(res?.msg || "操作失败", 'success', '温馨提示')
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            })
        },
        hanldRest() {
            const _this = this;
            _this.$useConfirm('是否确认重设附件库数据？', '温馨提示', 'error').then(() => {
                _this.sendRest();
            })
        }
    },
}
</script>
  
<style lang="scss" scoped>
.app-container {
    padding: 0 25px;
}
</style>