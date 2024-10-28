<template>
    <div class="update-container">
        <el-steps class="step-container" direction="vertical" :active="stepData.step" process-status="success">
            <el-step v-for="(item, index) in stepData.list" :key="index" :title="item.title" />
        </el-steps>
        <div class="content-container" v-if="pageData">
            <img :src="pageData.logo" class="logo" alt="">
            <div class="title">{{ pageData.title }} {{ pageData.version_name }}</div>
            <div class="loading">
                <vxe-icon name="refresh" class="loading-icon" roll></vxe-icon>
                <div>{{ stepData.stepText ? `正在${stepData.stepText}...` : '出现异常错误' }}</div>
            </div>
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
            stepData: {
                step: 0,
                stepText: '',
                status: 'process',
                list: [
                    {
                        step: 'download',
                        title: '下载安装包',
                    },
                    {
                        step: 'unzip',
                        title: '解压安装包',
                    },
                    {
                        step: 'updateData',
                        title: '数据安装',
                    },
                    {
                        step: 'success',
                        title: '安装完成',
                    },
                ],
            },
            pageData: null
        }
    },
    created() {
        this.detail()
    },
    methods: {
        exceStep(step) {
            const _this = this;
            const queryParams = {
                ..._this.ajaxParams,
            };
            const item = _this.stepData.list.find(item => item.step === step);
            _this.stepData.stepText = item.title;
            _this.stepData.step = _this.stepData.list.findIndex(item => item.step === step) + 1;
            _this.$http.usePost(`admin/Plugin/install?step=${step}`, queryParams).then((res) => {
                if (res?.code === 200) {
                    if (res.data.next === '') {
                        const stepIndex = _this.stepData.list.findIndex(item => item.step === 'success');
                        _this.stepData.step = stepIndex
                        setTimeout(() => {
                            _this.$emit("update:closeWin");
                            window.location.reload();
                        }, 2000);
                        _this.stepData.stepText = res.msg;
                        _this.$useNotify(res?.msg || "操作成功", 'success', '温馨提示');
                    } else if (res.data.next) {
                        _this.exceStep(res.data.next);
                    }
                } else {
                    _this.$useNotify(res?.msg || "操作失败", 'error', '温馨提示', {
                        'onClose': () => {
                            _this.$emit("update:closeWin");
                        }
                    })
                }
            }).catch((err) => {
                _this.$emit("update:closeWin");
                console.log('error', err);
            })
        },
        detail() {
            const _this = this;
            const queryParams = {
                ...this.ajaxParams,
            };
            _this.$http.useGet("admin/Plugin/detail", queryParams)
                .then((res) => {
                    if (res.code === 200) {
                        _this.pageData = res?.data ?? {};
                        _this.exceStep('download')
                    } else {
                        _this.$useNotify(res?.msg || "操作失败", 'error', '温馨提示', {
                            'onClose': () => {
                                _this.$emit("update:closeWin");
                            }
                        })
                    }
                })
                .catch((err) => {
                    if ([11000,666,600].includes(err?.code)) {
                        _this.openWin("remote/cloud/login");
                    } else {
                        _this.$emit("update:closeWin");
                    }
                });
        }
    },
};
</script>
  
<style lang="scss">
.update-container {
    display: flex;
    width: 100%;
    height: 100%;
    overflow: hidden;

    .step-container {
        display: flex;
        justify-content: flex-start;
        align-items: flex-start;
        padding: 20px;
        border-right: 1px solid #e5e5e5;
        height: 100%;
        overflow-y: hidden;
        overflow-x: hidden;
        box-sizing: border-box;
    }

    .content-container {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;

        .logo {
            width: 90px;
            height: 90px;
            border-radius: 10px;
        }

        .title {
            font-size: 20px;
            font-weight: 700;
            margin-top: 10px;
        }

        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 10px;

            .loading-icon {
                font-size: 22px;
            }
        }
    }
}
</style>
  