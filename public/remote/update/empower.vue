<template>
    <div class="empower-container">
        <div class="title">系统授权信息</div>
        <div class="empower-box">
            <div class="empower" v-if="detail?.title">
                <div class="logo-box">
                    <img class="logo" :src="detail?.logo || '/image/logo.png'" />
                    <div class="system-name">{{ detail?.title || '授权错误' }}</div>
                </div>
                <el-divider>系统授权信息</el-divider>
                <div class="item">
                    <div class="label">
                        系统版本
                    </div>
                    <div class="value">{{ detail?.system_info?.system_version_name || '版本错误' }}</div>
                </div>
                <div class="item">
                    <div class="label">
                        站点名称
                    </div>
                    <div class="value">{{ detail?.title || '授权错误' }}</div>
                </div>
                <div class="item">
                    <div class="label">
                        站点域名
                    </div>
                    <div class="value">{{ detail?.domain || '授权错误' }}</div>
                </div>
                <div class="item">
                    <div class="label">
                        服务地址
                    </div>
                    <div class="value">{{ detail?.ip || '授权错误' }}</div>
                </div>
                <div class="item">
                    <div class="label">
                        授权版本
                    </div>
                    <div class="value">
                        <el-tag type="success" v-if="detail?.is_auth == 1">商业版</el-tag>
                        <el-tag type="danger" v-else>免费版</el-tag>
                    </div> 
                </div>
            </div>
            <el-empty v-else description="未获取授权版本，或许未登录云服务" />
        </div>
    </div>
</template>

<script >
export default {
    data() {
        return {
            detail: {
                title: '',
                ip: '',
                domain: '',
                logo: '',
                is_auth: 0
            }
        }
    },
    methods: {
        getDetail() {
            const _this = this;
            _this.$http.usePut("admin/Updated/empower").then((res) => {
                const { data } = res;
                _this.detail = data;
            })
        }
    },
    mounted() {
        this.getDetail();
    }
}
</script>

<style lang="scss" scoped>
.empower-container {
    background: #fff;
    height: 100%;
    display: flex;
    flex-direction: column;

    .title {
        height: 50px;
        border-bottom: 1px solid #e5e5e5;
        display: flex;
        align-items: center;
        padding: 0 20px;
        font-size: 16px;
        font-weight: 700;
    }

    .empower-box {
        display: flex;
        justify-content: center;
        height: calc(100% - 50px);
        .empower {
            width: 500px;
            padding: 30px 20px;
            border-radius: 10px;
            margin-top: 50px;
            .logo-box{
                text-align: center;
                .logo{
                    width: 100px;
                    height: 100px;
                    border-radius: 10px;
                }
                .system-name{
                    font-weight: 700;
                    font-size: 28px;
                    padding-top: 10px;
                }
            }

            .item {
                padding: 15px 0;
                display: flex;
                justify-content: center;
                font-size: 14px;
                .label{
                    width: 200px;
                    text-align: center;
                }
                .value{
                    width: 200px;
                }
            }
        }
    }

}
</style>