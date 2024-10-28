<template>
    <div class="box-container">
        <div class="box-block">
            <div class="login-banner">
                <div class="title">用户数据管理</div>
                <img src="/image/storeLogin-img.png" alt="">
            </div>
            <div class="login-box">
                <div class="horn horn-t"></div>
                <div class="login-title">用户登录</div>
                <div class="item mb-10">
                    <n-input placeholder="请输入登录账号" v-model:value="form.username" @keydown.enter="login">
                        <template #prefix>
                            <AppIcons icon="UserOutlined" color="#FFFFFF" :size="20" />
                        </template>
                    </n-input>
                </div>
                <div class="item">
                    <n-input placeholder="请输入密码" v-model:value="form.password" type="password" show-password-on="click"
                        @keydown.enter="login">
                        <template #prefix>
                            <AppIcons icon="UnlockOutlined" color="#FFFFFF" :size="20" />
                        </template>
                    </n-input>
                </div>
                <!-- <div class="options">
                    <a>忘记密码</a>
                </div> -->
                <div class="submit">
                    <n-button class="login-submit" @click="login" :loading="loading" :disabled="loading">登录</n-button>
                </div>
                <div class="horn horn-b"></div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data: () => {
        return {
            loading: false,
            form: {
                username: '',
                password: ''
            }
        }
    },
    created() {
        this.moudelName = globalThis.location.pathname;
    },
    methods: {
        // 登录
        login() {
            const _this = this;
            _this.loading = true;
            _this.$http.usePost(_this.moudelName + 'Publics/login', this.form).then(res => {
                _this.$useNotification?.success({
                    title: res?.msg ?? "获取失败",
                    duration: 1500,
                });
                setTimeout(() => {
                    _this.$router.push('/Index/index?token=' + res.data.token);
                }, 300);
            }).finally(() => {
                _this.loading = false;
            })
        }
    }
}
</script>
<style lang="scss">
.box-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background: radial-gradient(ellipse at 75% 70%, rgb(48, 44, 127) 0%, #3952c2 100%);

    .box-block {
        margin: 0 auto;
        display: flex;
        align-items: center;

        .login-banner {
            flex: 1;
            text-align: center;

            img {
                width: 80%;
                margin: 0 auto;
                animation: loginBannerAn 20s infinite 5s;
            }

            @keyframes loginBannerAn {
                0% {
                    transform: translateX(0) scale(1);
                }

                25% {
                    transform: translateX(-5%) scale(1.1);
                }

                50% {
                    transform: translateX(0%) scale(1);
                }

                75% {
                    transform: translateX(5%) scale(1.1);
                }

                100% {
                    transform: translateX(0) scale(1);
                }
            }

            .title {
                font-size: 2.75vw;
                color: #fff;
                letter-spacing: 10px;
                font-weight: bold;
            }
        }

        .login-box {
            width: 400px;
            padding: 60px 40px 80px 40px;
            background: rgba(48, 44, 127, 0.45);
            backdrop-filter: blur(10px);
            position: relative;

            .horn::after,
            .horn::before {
                position: absolute;
                content: "";
                width: 50px;
                height: 50px;
                border: solid 6px rgba(57, 82, 194, 0.45);
            }

            .horn.horn-t::after {
                top: 0;
                left: 0;
                // 斜着裁剪一半
                border-right-color: transparent;
                border-bottom-color: transparent;
            }

            .horn.horn-b::after {
                left: 0;
                bottom: 0;
                // 斜着裁剪一半
                border-right-color: transparent;
                border-top-color: transparent;
            }

            .horn.horn-t::before {
                top: 0;
                right: 0;
                // 斜着裁剪一半
                border-left-color: transparent;
                border-bottom-color: transparent;
            }

            .horn.horn-b::before {
                right: 0;
                bottom: 0;
                // 斜着裁剪一半
                border-left-color: transparent;
                border-top-color: transparent;
            }

            .login-title {
                font-size: 18px;
                color: #fff;
                padding-bottom: 80px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
                letter-spacing: 4px;
            }

            .item {
                padding: 10px 0;
                border-bottom: #DDDDDD solid 1px;

                .n-input {
                    --n-color: transparent !important;
                    --n-color-focus: transparent !important;
                    --n-border: none !important;
                    --n-border-hover: none !important;
                    --n-border-focus: none !important;
                    --n-box-shadow-focus: none !important;
                    --n-text-color: #FFFFFF !important;
                    --n-padding-left: 0 !important;

                    .n-input__prefix {
                        margin-right: 15px;
                    }

                    .n-input__input-el {
                        font-size: 16px;
                        font-weight: 600;
                        letter-spacing: 2px;
                    }
                }
            }

            .options {
                a {
                    color: #fff;
                }
            }

            .submit {
                padding-top: 40px;

                .login-submit {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: 600 !important;
                    --n-color: #3b52c0 !important;
                    --n-color-hover: rgba(59, 82, 192, .75) !important;
                    --n-color-pressed: rgba(78, 107, 255, 0.75) !important;
                    --n-color-focus: rgba(78, 107, 255, 0.75) !important;
                    --n-color-disabled: rgba(78, 107, 255, 0.75) !important;
                    --n-ripple-color: #3b52c0 !important;
                    --n-text-color: #FFFFFF !important;
                    --n-text-color-hover: #FFFFFF !important;
                    --n-text-color-pressed: #FFFFFF !important;
                    --n-text-color-focus: #FFFFFF !important;
                    --n-text-color-disabled: rgb(51, 54, 57) !important;
                    --n-border: 1px solid #3b52c0 !important;
                    --n-border-hover: 1px solid rgba(59, 82, 192, .75) !important;
                    --n-border-pressed: 1px solid rgba(78, 107, 255, 0.75) !important;
                    --n-border-focus: 1px solid rgba(78, 107, 255, 0.75) !important;
                    --n-border-disabled: 1px solid rgba(78, 107, 255, 0.75) !important;
                    --n-width: 100% !important;
                    --n-height: 45px !important;
                    --n-font-size: 16px !important;
                    --n-border-radius: 0 !important;
                }
            }
        }
    }
}

.mb-10 {
    margin-bottom: 20px;
}
</style>