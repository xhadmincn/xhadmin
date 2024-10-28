<template>
    <div class="h-100% app-container">
        <div class="info-container">
            <div class="flex items-center">
                <n-button type="warning" @click="toBack">返回</n-button>
            </div>
            <div class="flex items-center">{{ platform.configs?.web_name }}【{{ platform?.platform_type_text ?? '应用' }}】
            </div>
        </div>
        <div class="app-list">
            <div class="item" v-for="(item, index) in platformApp" :key="index">
                <div class="app-item">
                    <img :src="item.logo" class="logo" alt="">
                    <div class="status">{{ item.status === '1' ? '使用中' : '已停用' }}</div>
                    <div class="button-group">
                        <n-tooltip trigger="hover" placement="right">
                            <template #trigger>
                                <div class="action-button" @click="hanldAdmin(item)">
                                    <AppIcons icon="PartitionOutlined" :size="20" color="#888" />
                                </div>
                            </template>
                            登录应用管理
                        </n-tooltip>
                        <n-tooltip trigger="hover" placement="right">
                            <template #trigger>
                                <div class="action-button" @click="copyAppsUrl(item)">
                                    <AppIcons icon="LinkOutlined" :size="20" color="#888" />
                                </div>
                            </template>
                            复制应用连接
                        </n-tooltip>
                        <n-tooltip trigger="hover" placement="right">
                            <template #trigger>
                                <div class="action-button" @click="hanldEdit(item)">
                                    <AppIcons icon="EditOutlined" :size="20" color="#888" />
                                </div>
                            </template>
                            修改应用信息
                        </n-tooltip>
                        <n-tooltip trigger="hover" placement="right">
                            <template #trigger>
                                <div class="action-button" @click="hanldStop(item)">
                                    <AppIcons icon="SettingOutlined" :size="20" color="#888" />
                                </div>
                            </template>
                            停止或启用应用
                        </n-tooltip>
                    </div>
                    <div class="title">{{ item.title }}</div>
                </div>
            </div>
            <div class="item action" @click="hanldShowDialog">
                <AppIcons icon="PlusOutlined" :size="48" color="#888" />
            </div>
        </div>
        <!-- 组件弹窗 -->
        <n-modal v-model:show="modalDialog.show" v-bind="modalDialog">
            <n-form :model="form" labn-position="top" class="form-container">
                <n-grid :cols="2" :x-gap="24" class="form">
                    <n-grid-item>
                        <n-form-item label="应用名称">
                            <n-input placeholder="请输入应用名称" v-model:value="form.title" />
                        </n-form-item>
                    </n-grid-item>
                    <n-grid-item>
                        <n-form-item label="应用选择">
                            <n-select v-model:value="form.name" :options="pluginOptions" placeholder="请选择应用" />
                        </n-form-item>
                    </n-grid-item>
                    <n-grid-item>
                        <n-form-item label="管理员账户">
                            <n-input placeholder="请输入管理员账户" v-model:value="form.username" />
                        </n-form-item>
                    </n-grid-item>
                    <n-grid-item>
                        <n-form-item label="管理员密码">
                            <n-input placeholder="请输入管理员密码" v-model:value="form.password" />
                        </n-form-item>
                    </n-grid-item>
                    <n-grid-item>
                        <n-form-item label="应用图标">
                            <div class="upload-container">
                                <div class="upload">
                                    <div class="item" v-if="form.logo">
                                        <n-image :src="form.logo" class="logo" />
                                    </div>
                                    <div class="item">
                                        <n-upload v-bind="upload">
                                            <div class="uploadify">
                                                <AppIcons icon="PlusOutlined" :size="42" color="#888" />
                                            </div>
                                        </n-upload>
                                    </div>
                                </div>
                            </div>
                        </n-form-item>
                    </n-grid-item>
                </n-grid>
                <div class="buttons">
                    <n-button type="success" @click="hanldConfirm">确定</n-button>
                    <n-button type="warning" @click="hanldCancel">取消</n-button>
                </div>
            </n-form>
        </n-modal>
    </div>
</template>

<script>
    export default {
        props: {
            modelValue: Array,
        },
        data() {
            return {
                platform: {},
                platformApp: [],
                modalDialog: {
                    show: false,
                    maskClosable: false,
                    preset: 'dialog',
                    title: '操作应用',
                    icon: () => undefined,
                    showIcon: false,
                    to: '.app-container',
                    class: 'app-dialog',
                },
                pluginList: [],
                pluginOptions: [],
                form: {
                    id: '',
                    title: '',
                    name: '',
                    logo: '',
                    username: '',
                    password: '',
                },
                upload: {
                    action: 'SystemUpload/upload',
                    headers: {},
                    showFileList: false,
                    data: {},
                    onError: this.onError,
                    onFinish: this.onFinish,
                },
            }
        },
        created() {
            this.initify();
        },
        methods: {
            // 上传结束
            onFinish(e) {
                const res = JSON.parse(e.event.currentTarget.response)
                if (res.code === 200) {
                    const { data } = res
                    this.form.logo = data?.url ?? ''
                    this.$useNotification?.success({
                        title: res?.msg ?? '获取失败',
                        duration: 1500,
                    });
                } else {
                    this.$useNotification?.error({
                        title: res?.msg ?? '获取失败',
                        duration: 1500,
                    });
                }
            },
            // 错误回调
            onError(e) {
                console.error(e);
                this.$useNotification?.error({
                    title: '上传失败',
                    duration: 1500,
                });
            },
            // 显示模态框
            hanldShowDialog() {
                this.modalDialog.show = true
            },
            hanldAdmin(e) {
                const _this = this;
                const query = {
                    app_id: e.id,
                }
                _this.$http.usePost('store/PlatformApp/login', query).then((res) => {
                    if (res.code === 200) {
                        const { data } = res;
                        if (data?.url) {
                            window.open(data?.url)
                        } else {
                            _this.$useNotification?.error({
                                title: '登录应用管理失败',
                                duration: 1500,
                            });
                        }
                    } else {
                        _this.$useNotification?.error({
                            title: res?.msg ?? '获取数据失败',
                            duration: 1500,
                        });
                    }
                })
            },
            copyAppsUrl(e) {
                const copyText = '//' + window.location.host + '/app/' + e.name + '/#/?appid=' + e.id;
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(copyText).then(_ => {
                        this.$useNotification?.success({
                            title: '已复制',
                            duration: 1500,
                        });
                    })
                } else {
                    let input = document.createElement('input');
                    input.value = copyText;
                    document.body.appendChild(input);
                    input.select();
                    document.execCommand('Copy');
                    input.remove();
                    this.$useNotification?.success({
                        title: '已复制',
                        duration: 1500,
                    });
                }
            },
            hanldEdit(e) {
                const _this = this;
                const query = {
                    app_id: e.id,
                }
                _this.$http.useGet('store/PlatformApp/edit', query).then((res) => {
                    if (res.code === 200) {
                        const { data } = res;
                        _this.form = data;
                        _this.modalDialog.show = true
                    } else {
                        _this.hanldCancel();
                        _this.$useNotification?.error({
                            title: res?.msg ?? '获取数据失败',
                            duration: 1500,
                        });
                    }
                })
            },
            // 停止或启用
            hanldStop(e) {
                const _this = this;
                _this.$useDialog.create({
                    type: 'warning',
                    title: '温馨提示',
                    content: '是否确定设置该应用状态？',
                    positiveText: '确定',
                    negativeText: '取消',
                    maskClosable: false,
                    onPositiveClick() {
                        const query = {
                            platform_id: e.platform_id,
                            app_id: e.id
                        }
                        _this.$http.useDelete('store/PlatformApp/status', query).then((res) => {
                            if (res.code === 200) {
                                _this.getPlatforms();
                                _this.$useNotification?.success({
                                    title: res?.msg ?? '操作成功',
                                    duration: 1500,
                                });
                            } else {
                                _this.$useNotification?.error({
                                    title: res?.msg ?? '操作失败',
                                    duration: 1500,
                                });
                            }
                        })
                    }
                });
            },
            // 弹窗确定
            hanldConfirm() {
                const _this = this;
                const query = {
                    platform_id: _this.$routeApp.query?.id,
                    ..._this.form
                }
                const url = _this.form?.id ? `store/PlatformApp/edit?app_id=${_this.form.id}` : 'store/PlatformApp/add'
                _this.$http.usePost(url, query).then((res) => {
                    _this.getPlatforms();
                    _this.hanldCancel();
                    _this.$useNotification?.success({
                        title: res?.msg ?? '操作成功',
                        duration: 1500,
                    });
                })
            },
            // 弹窗取消
            hanldCancel() {
                const _this = this;
                _this.form = {
                    title: '',
                    name: '',
                    logo: '',
                };
                _this.modalDialog.show = false;
            },
            toBack() {
                this.$routerApp.back()
            },
            detail() {
                const _this = this;
                const query = {
                    id: _this.$routeApp.query?.id
                }
                _this.$http.useGet('store/Platform/detail', query).then((res) => {
                    const { data } = res;
                    _this.platform = data;
                })
            },
            // 获取平台旗下应用
            getPlatforms() {
                const _this = this;
                const query = {
                    id: _this.$routeApp.query?.id
                }
                _this.$http.useGet('store/PlatformApp/index', query).then((res) => {
                    const { data } = res;
                    _this.platformApp = data;
                })
            },
            // 获取已安装应用
            getPluginsApps() {
                // 获取平台详情
                const platform = this.platform;
                // 已授权应用名称
                const plugins_names = this.$userApp.userInfo.plugins;
                // 已安装应用列表
                const pluginList = this.pluginList;
                let pluginOptions = [];
                for (let index = 0; index < pluginList.length; index++) {
                    const item = pluginList[index];
                    const _plugin = plugins_names.find((plugin_name) => {
                        if (
                            plugin_name === item.name
                            &&
                            item.platform.find((inst_platform) => inst_platform === platform.platform_type)
                        ) {
                            return plugin_name
                        }
                    })
                    if (_plugin) {
                        pluginOptions.push({
                            label: `${item.title} -- [${item.platform_text}]`,
                            value: item.name
                        })
                    }
                }
                this.pluginOptions = pluginOptions;
            },
            // 获取插件列表
            getPlugins() {
                const _this = this;
                _this.$http.useGet('store/PlatformApp/plugins').then((res) => {
                    const { data } = res;
                    _this.pluginList = data;
                    _this.getPluginsApps();
                })
            },
            initify() {
                this.upload.headers = {
                    Authorization: this.$userApp.token ?? ''
                }
                this.detail()
                this.getPlatforms()
                this.getPlugins()
            },
        },
    }
</script>

<style lang="scss" scoped>
    .app-dialog {
        width: 45%;
    }

    .upload-container {
        display: block;
        width: 100%;

        .upload {
            display: flex;
            gap: 18px;

            .item {
                .logo {
                    width: 70px !important;
                    height: 70px !important;
                    border-radius: 10px;
                }
            }

        }
    }

    .uploadify {
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        border: 1px solid #d1d1d1;
        background: #fff;
        user-select: none;
        cursor: pointer;
    }

    .uploadify:hover {
        background: #f1f1f1;
    }

    .app-container {
        display: flex;
        flex-direction: column;
        background: #fff;

        .form-container {
            height: 450px;
            display: flex;
            flex-direction: column;

            .form {
                padding-top: 20px;
            }

            .buttons {
                display: flex;
                flex-wrap: wrap;
                gap: 24px;
                justify-content: center;
                align-items: center;
            }
        }

        .info-container {
            display: flex;
            justify-content: space-between;
            height: 50px;
            padding: 0 20px;
            border-bottom: 1px solid #e5e5e5;
        }

        .app-list {
            padding: 10px;
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-wrap: wrap;

            .item {
                margin: 5px 40px 5px 5px;

                .app-item {
                    position: relative;
                    width: 120px;
                    height: 120px;

                    .logo {
                        border-radius: 5px;
                    }

                    .status {
                        position: absolute;
                        top: 0;
                        left: 0;
                        padding: 3px;
                        font-size: 12px;
                        color: #fff;
                        background: rgba(#000000, 0.6);
                    }

                    .button-group {
                        position: absolute;
                        top: 0;
                        right: -35px;
                        bottom: 0;
                        display: flex;
                        flex-direction: column;
                        justify-content: space-between;
                        padding: 0 8px;
                        user-select: none;
                        cursor: pointer;
                    }

                    .title {
                        text-align: center;
                        position: absolute;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        color: #fff;
                        background: rgba(#000000, 0.5);
                        user-select: none;
                        border-bottom-left-radius: 5px;
                        border-bottom-right-radius: 5px;
                    }
                }
            }


            .action {
                width: 120px;
                height: 120px;
                border: 1px solid #e1e1e1;
                border-radius: 5px;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .action:hover {
                background: #f5f5f5;
            }
        }
    }
</style>