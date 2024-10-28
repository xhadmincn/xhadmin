<template>
    <div class="bg-white page">
        <div class="form">
            <div class="py-10">
                <h4>应用主域名</h4>
                <p class="tips">应用主域名（落地域名）设置以后访问域名就能直接访问到您的应用，对外分享链接使用</p>
                <p class="tips">如没有域名可填写主站域名：<span class="web_url" @click="form.domain = web_url">{{ web_url
                }}</span>，主站域名不参与落地域名功能</p>
                <p class="tips">在代码中使用：getHpConfig('domain', (int)$saas_appid, 'plugins_domains');</p>
                <el-input v-model="form.domain" size="large" placeholder="应用主域名，如：https://www.baidu.com，不以/结尾" />
            </div>
            <div v-if="form.domain">
                <div>
                    <h4>应用辅助域名({{ form.sub_domain.length }}/10)</h4>
                    <p class="tips">应用辅助域名设置以后访问域名就能直接访问到您的应用，可添加多个</p>
                    <p class="tips">在代码中使用：getHpConfig('sub_domain', (int)$saas_appid, 'plugins_domains');</p>
                </div>
                <div v-for="(item, index) in form.sub_domain" class="flex mb-4">
                    <el-input v-model="item" placeholder="应用辅助域名" />
                    <el-button type="danger" @click="removeSubDomain(index)" class="ml-4">移除</el-button>
                </div>
                <div class="flex" v-if="form.sub_domain.length < 10">
                    <el-input v-model="sub_domain" placeholder="应用辅助域名" />
                    <el-button type="success" @click="addSubDomain" class="ml-4">添加</el-button>
                </div>
            </div>
        </div>
        <div class="flex py-10">
            <el-button type="primary" @click="onSubmit" size="large">保存</el-button>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            web_url: '',
            baseUrl: '',
            sub_domain: '',
            form: {
                domain: '',
                sub_domain: [],
            }
        }
    },
    mounted() {
        this.web_url = this.$siteApp.siteInfo.web_url;
        this.baseUrl = `app/${this.$siteApp.siteInfo.plugin}/`;
        this.initify()
    },
    methods: {
        addSubDomain() {
            if (this.sub_domain && this.form.sub_domain.length < 10) {
                this.form.sub_domain.push(this.sub_domain);
                this.sub_domain = '';
            }
        },
        removeSubDomain(index) {
            this.form.sub_domain.splice(index, 1);
        },
        initify() {
            this.$http.useGet(this.baseUrl + 'admin/PluginsDomains/settings').then((res) => {
                this.form = res.data;
            }).catch((err) => {
            })
        },
        onSubmit() {
            this.$http.usePost(this.baseUrl + 'admin/PluginsDomains/settings', this.form).then((res) => {
                this.$message.success('保存成功');
                this.$siteApp.getSite();
                this.initify();
            }).catch((err) => {
            })
        }
    },
}
</script>

<style lang="scss">
.page {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.form {
    width: max(60%, 400px);
    flex: 1;
    padding: 0 60px;
    overflow-y: auto;
}

.py-10 {
    padding: 40px 0;
}

.ml-4 {
    margin-left: 20px;
}

.mb-4 {
    margin-bottom: 20px;
}

.tips {
    color: var(--el-text-color-placeholder);
    font-size: 12px;
    margin-top: 10px;
    margin-bottom: 20px;
}

.web_url {
    color: var(--el-text-color-primary);
    font-size: 12px;
    cursor: pointer;
}
</style>