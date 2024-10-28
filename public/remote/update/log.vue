<template>
    <div class="log-container">
        <div class="log-title">框架更新日志</div>
        <div class="log-content">
            <el-timeline v-if="datalist.length">
                <el-timeline-item
                :timestamp="`${item?.create_at} --- ${item.version_name}（${item.version}）`"
                placement="top"
                v-for="item in datalist"
                :key="item"
                center
                >
                    <el-card>
                        <pre class="log-line-content">{{ item?.content }}</pre>
                    </el-card>
                </el-timeline-item>
            </el-timeline>
            <el-empty v-else description="未获取授权版本，或许未登录云服务" />
        </div>
    </div>
</template>
  
<script>
export default {
    data() {
        return {
            datalist: []
        }
    },
    methods: {
        getList() {
            const _this = this;
            _this.$http.usePut("admin/Updated/index").then((res) => {
                const { data } = res;
                _this.datalist = data;
            })
        }
    },
    mounted() {
        this.getList();
    },
}
</script>
  
<style lang="scss" scoped>
.log-container {
    background: #fff;
    height: 100%;
    display: flex;
    flex-direction: column;

    .log-title {
        height: 60px;
        display: flex;
        align-items: center;
        font-size: 20px;
        font-weight: 600;
        padding: 0 20px;
        border-bottom: 1px solid #e5e5e5;
    }

    .log-content {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        overflow-x: hidden;
    }
}

.log-line-content {
    display: block;
    width: 100%;
    font-size: 14px;
    color: #555;
    white-space: pre-wrap;
    word-break: break-word;
}
</style>
  