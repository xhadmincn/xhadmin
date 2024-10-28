<template>
    <div class="update-container">
        <div class="update-box">
            <div class="logo-container">
                <img :src="pageData.logo" class="logo" />
            </div>
            <div class="title">
                {{ pageData?.title }}
            </div>
            <div class="update-config-container">
                <div class="item" :class="{ active: tabs.active === index }" v-for="(item, index) in tabs.list"
                    :key="index" @click="hanldSelect(index)">
                    {{ item }}
                </div>
            </div>
            <!-- 版本发布 -->
            <div class="body" v-if="tabs.active === 0">
                <div class="item">
                    <div class="label">版本信息</div>
                    <div class="value">
                        <el-input :value="`${version?.version_name}（${version?.version}）`" disabled />
                    </div>
                </div>
                <div class="item">
                    <div class="label">版本描述</div>
                    <div class="value">
                        <el-input v-model="pageData.desc" type="textarea" placeholder="版本描述（选填）" rows="8" />
                    </div>
                </div>
                <!-- <div class="item">
                    <div class="label">发布预览</div>
                    <div class="value">
                        <el-switch v-model="formData.applet_state" inactive-value="10" active-value="20">
                            <template #checked>
                                二维码预览
                            </template>
                            <template #unchecked>
                                无需预览
                            </template>
                        </el-switch>
                    </div>
                </div> -->
                <div class="item">
                    <div class="label"></div>
                    <div class="value">
                        <el-button type="primary" @click="hanldSubmit"
                            :disabled="(!formData.douyin_appid || !formData.douyin_secret || !formData.douyin_email || !formData.douyin_password) || submitStatus">
                            {{ submitStatus ? '提交中...' : '立即发布' }}
                        </el-button>
                    </div>
                </div>
            </div>
            <!-- 参数配置 -->
            <div class="body" v-if="tabs.active === 1">
                <div class="item">
                    <div class="label">APPID</div>
                    <div class="value">
                        <el-input placeholder="请输入APPID" :clearable="true" v-model="formData.douyin_appid" />
                    </div>
                </div>
                <div class="item">
                    <div class="label">Secret</div>
                    <div class="value">
                        <el-input placeholder="请输入Secret" :clearable="true" v-model="formData.douyin_secret" />
                    </div>
                </div>
                <div class="item">
                    <div class="label">开发者邮箱</div>
                    <div class="value">
                        <el-input placeholder="请输入抖音开发者邮箱" :clearable="true" v-model="formData.douyin_email" />
                    </div>
                </div>
                <div class="item">
                    <div class="label">开发者密码</div>
                    <div class="value">
                        <el-input placeholder="请输入抖音开发者密码" :clearable="true" v-model="formData.douyin_password" />
                    </div>
                </div>
                <div class="item">
                    <div class="label"> </div>
                    <div class="value">
                    <el-button type="success" @click="hanldSave" :disabled="submitStatus">
                        {{ submitStatus ? '提交中...' : '保存设置' }}
                    </el-button>
                    </div>
                </div>
            </div>
        </div>
        <!-- 预览二维码弹窗 -->
        <div class="priview-modal" v-if="pageData.preview && pageData.qrcode">
            <div class="preview-qrcode-box">
                <div class="preview-text">扫二维码预览</div>
                <img :src="pageData.qrcode" class="preview-qrcode" alt="">
                <button class="preview-close" @click="pageData.preview = false">关闭</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            tabs: {
                active: 0,
                list: [
                    '版本发布',
                    '参数配置',
                ],
            },
            submitStatus: false,
            pageData: {
                logo: '',
                desc:'',
                preview: false,
            },
            version:{
                version:'',
                version_name:'',
            },
            formData: {
                douyin_appid: '',
                douyin_secret: '',
                douyin_email: '',
                douyin_password: '',
                douyin_state: '10',
            }
        }
    },
    mounted() {
        this.initify();
    },
    methods: {
        // 保存配置
        hanldSave() {
            const _this = this;
            _this.submitStatus = true;
            const api = this.$moduleName + '/Applet/douyinConfig'
            this.$http.usePut(api, this.formData).then((res) => {
                _this.$useNotify(res?.msg ?? '操作成功','success','温馨提示')
            }).finally(() => {
                _this.submitStatus = false;
            })
        },
        // 发布更新
        hanldSubmit() {
            const _this = this;
            _this.submitStatus = true;
            const loading = _this.$useLoading('发布中...',{
                background: 'rgba(0, 0, 0, 0.7)',
            })
            const api = this.$moduleName + '/Applet/douyinPublish'
            const params = {
                preview: this.formData.applet_state,
                desc:this.pageData.desc,
            };
            this.$http.usePost(api, params).then((res) => {
                if (res?.data?.preview) {
                    _this.pageData.preview = true;
                    _this.pageData.qrcode = res?.data?.qrcode ?? '';
                }
                // 预览二维码
                _this.$useNotify(res?.msg ?? '操作成功')
            }).finally(() => {
                _this.submitStatus = false;
                loading.close();
            })
        },
        hanldSelect(index) {
            this.tabs.active = index
        },
        initify() {
            const api = this.$moduleName + '/Applet/douyinConfig'
            this.$http.useGet(api).then((res) => {
                const { data } = res;
                this.formData = {
                    ...this.formData,
                    ...data?.config ?? {},
                };
                this.pageData = {
                    ...data?.app
                };
                this.version = {
                    ...this.version,
                    ...data?.version
                };
            })
        }
    },
}
</script>

<style lang="scss" scoped>
.update-container {
    width: 100%;
    height: 100%;
    background-color: #fff;

    .update-box {
        width: 500px;
        height: 100%;
        margin: 0 auto;
        padding-top: 50px;

        .logo-container {
            text-align: center;

            .logo {
                width: 80px;
                height: 80px;
                border-radius: 5px;
                margin: 0 auto;
            }
        }

        .title {
            font-size: 22px;
            color: #333;
            margin-top: 10px;
            text-align: center;
        }

        .update-config-container {
            display: flex;
            margin-bottom: 15px;
            border-bottom: 1px solid #e5e5e5;

            .item {
                width: 80px;
                color: #555;
                cursor: pointer;
                padding: 15px 0;
                font-size: 14px;
                text-align: center;
                user-select: none;
                border-bottom: 1px solid transparent;
                font-weight:700;
            }

            .active {
                border-bottom: 1px solid #409eff;
            }
        }

        .body {
            display: flex;
            flex-direction: column;
            gap: 20px 0;

            .item {
                display: flex;
                align-items: center;
                justify-content: center;

                .label {
                    width: 80px;
                    text-align: right;
                    margin-right: 10px;
                    color: #666;
                }

                .value {
                    flex: 1;
                    text-align: left;
                    color: #333;
                }
            }

            .submit {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 20px;
                margin-top: 20px;
            }
        }
    }

    .priview-modal {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .6);

        .preview-qrcode-box {
            width: 300px;
            height: 300px;
            background: #fff;
            border-radius: 5px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px 0;

            .preview-text {
                font-size: 15px;
                color: #444;
                font-weight: 700;
            }

            .preview-qrcode {
                width: 210px;
                height: 210px;
            }

            .preview-close {
                background: #8d4be9;
                color: #fff;
                border-radius: 3px;
                transition: all .3s;
                padding: 2px 25px;

                &:hover {
                    background: #722ED1;
                }
            }
        }
    }
}
</style>