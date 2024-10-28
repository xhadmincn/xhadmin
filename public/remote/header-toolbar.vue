<template>
    <div class="h-100%">
        <!-- 弹窗提示有版本更新 -->
        <Teleport to="body">
            <div class="updated-version" v-if="updateInfo.status">
                <div class="updated-box">
                    <img src="/image/updated-bg.png" class="updated-bg" />
                    <div class="updated-title">
                        发现新版本更新，发布时间：{{ updateInfo.detail.created_at }}
                    </div>
                    <div class="updated-content-box">
                        <div class="version-box">
                            <div class="updated-version-title">
                                当前：{{ updateInfo.detail.client_version_name }}（{{ updateInfo.detail.client_version }}）
                            </div>
                            <div class="updated-version-title next-version">
                                下个版本：{{ updateInfo.detail.version_name }}（{{ updateInfo.detail.version }}）
                            </div>
                        </div>
                        <pre class="updated-content">{{ updateInfo.detail.content }}</pre>
                    </div>
                    <div class="updated-buttons">
                        <button class="action-button to-btn" @click="toUpdated('/Updated/updateCheck')">立即去更新</button>
                        <button class="action-button cancel-btn" @click="hanldCancel">忽略本次更新</button>
                    </div>
                </div>
            </div>
        </Teleport>
        <!-- 工具栏 -->
        <div v-if="toolbar.length" class="xhadmin-header-tools">
            <div class="item" v-for="(item, index) in toolbar" :key="index" @click="item.hanlder">
                <AppIcons class="icon" :icon="item.icon" :size="17" :color="item.isUpdate ? '#ff0000' : '#555'" />
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            // 是否全屏
            fullscreen: false,
            // 是否有版本更新
            updateInfo: {
                status: false,
                detail: {
                    version: 0,
                    version_name: '',
                    content: '',
                    client_version: 0,
                    client_version_name: '',
                },
            },
            // 工具栏
            toolbar: [
                {
                    title: '刷新页面',
                    name: 'refresh',
                    icon: 'Refresh',
                    hanlder: () => {
                        window.location.reload()
                    },
                },
                {
                    title: '渠道中心',
                    name: 'home',
                    icon: 'Monitor',
                    hanlder: () => {
                        window.open('/')
                    },
                },
                {
                    title: '全屏缩放',
                    name: 'zoom',
                    icon: 'ExpandOutlined',
                    hanlder: () => {
                        const element = document.documentElement
                        // 如果是全屏状态
                        if (this.fullscreen) {
                            // 如果浏览器有这个Function
                            if (document.exitFullscreen) {
                                document.exitFullscreen()
                            } else if (document.webkitCancelFullScreen) {
                                document.webkitCancelFullScreen()
                            } else if (document.mozCancelFullScreen) {
                                document.mozCancelFullScreen()
                            } else if (document.msExitFullscreen) {
                                document.msExitFullscreen()
                            }
                        } else {
                            // 如果浏览器有这个Function
                            if (element.requestFullscreen) {
                                element.requestFullscreen()
                            } else if (element.webkitRequestFullScreen) {
                                element.webkitRequestFullScreen()
                            } else if (element.mozRequestFullScreen) {
                                element.mozRequestFullScreen()
                            } else if (element.msRequestFullscreen) {
                                element.msRequestFullscreen()
                            }
                        }
                        // 判断全屏状态的变量
                        this.fullscreen = !this.fullscreen
                    },
                },
            ]
        }
    },
    mounted() {
        this.initify()
    },
    methods: {
        // 跳转版本升级页面
        toUpdated(path) {
            this.updateInfo.status = false
            this.$routerApp.push(path)
        },
        // 检测版本升级并弹窗
        checking() {
            const _this = this
            const currentRoute = _this.$routeApp.path
            // 已经在更新页面，不再重复请求
            if (currentRoute.includes('/Updated/')) {
                return;
            }
            _this.$http.useDelete('admin/Updated/updateCheck').then((res) => {
                const { data } = res
                // 无版本升级
                if (data?.version <= data?.client_version) {
                    return;
                }
                // 存在忽略版本更新
                const ignoreVersion = parseInt(localStorage.getItem('ignore_system_version'))
                if (ignoreVersion && ignoreVersion === data.version) {
                    return;
                }
                // 有新版本，弹窗提示
                _this.updateInfo = {
                    status: true,
                    detail: data
                }
            }).catch((err) => {
                // 未登录
                if (err?.code === 666) {
                    _this.$useRemote('remote/cloud/login', {}, {
                        title: '云服务登录',
                        customStyle: {
                            width: '480px',
                            height: '430px',
                        },
                    })
                }
            })
        },
        // 取消本次更新
        hanldCancel() {
            const _this = this;
            localStorage.setItem("ignore_system_version", _this.updateInfo.detail.version);
            window.location.reload();
        },
        initify() {
            const _this = this
            // 检测是否有版本更新
            _this.checking()
        }
    },
}
</script>

<style lang="scss">
.updated-version {
    position: fixed;
    inset: 0;
    background: rgba(#000, .4);
    -webkit-backdrop-filter: blur(6px);
    backdrop-filter: blur(6px);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;

    .updated-box {
        width: 400px;
        display: flex;
        flex-direction: column;
        background: #fff;
        box-shadow: 2px 2px 10px rgba(#000, .1);

        .updated-bg {
            height: 100px;
            background: #722ED1;
            object-fit: cover;
        }

        .updated-title {
            padding: 10px 15px;
            display: flex;
            align-items: center;
            font-size: 14px;
            color: #333;
            font-weight: 700;
        }

        .updated-content-box {
            border-top: 1px solid #e5e5e5;
            border-bottom: 1px solid #e5e5e5;
            padding: 0 15px;
            height: 280px;
            overflow-y: auto;
            overflow-x: hidden;

            .version-box {
                display: flex;
                gap: 10px;
                color: #333;
                font-size: 12px;
                padding: 5px 0;

                .updated-version-title {}

                .next-version {
                    color: #ff5900;
                }
            }

            .updated-content {
                line-height: 25px;
                font-size: 12px;
                color: #666;
                display: block;
                width: 100%;
                white-space: pre-wrap;
                word-break: break-word;
            }
        }

        .updated-buttons {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px 0;
            gap: 20px;

            .action-button {
                color: #fff;
                padding: 8px 15px;
                border-radius: 4px;
                font-size: 12px;
                cursor: pointer;
                border: none;
            }

            .to-btn {
                background: #722ED1;

                &:hover {
                    background: #a065e9;
                }
            }

            .cancel-btn {
                background: #FF7D00;

                &:hover {
                    background: #ff9d00;
                }
            }
        }
    }
}

@keyframes logo-animation {
    0% {
        -webkit-transform: scale(0);
        transform: scale(0)
    }

    80% {
        -webkit-transform: scale(1.2);
        transform: scale(1.2)
    }

    to {
        -webkit-transform: scale(1);
        transform: scale(1)
    }
}

.xhadmin-header-tools {
    height: 100%;
    display: flex;
    justify-content: flex-end;
    align-items: center;

    .item {
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        user-select: none;
        cursor: pointer;
        padding: 0 10px;

        &:hover {
            .icon {
                -webkit-animation: logo-animation .3s ease-in-out;
                animation: logo-animation .3s ease-in-out;
            }
        }
    }
}
</style>