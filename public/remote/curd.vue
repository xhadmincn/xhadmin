<template>
    <div class="box-container">
        <n-form>
            <n-grid x-gap="12" :cols="2">
                <n-gi>
                    <n-form-item label="构建至应用">
                        <n-select :options="plugins" :on-update:value="hanldSelectApp" />
                    </n-form-item>
                </n-gi>
                <n-gi>
                    <n-form-item label="构建至菜单">
                        <n-cascader :options="menus" @update:value="hanldSelectMenu" />
                    </n-form-item>
                </n-gi>
                <n-gi>
                    <n-form-item label="是否强制覆盖" class="flex-1">
                        <n-switch v-model:value="formData.is_cover" :unchecked-value="0" :checked-value="1">
                            <template #checked>
                                覆盖
                            </template>
                            <template #unchecked>
                                不覆盖
                            </template>
                        </n-switch>
                    </n-form-item>
                </n-gi>
                <n-gi v-if="formData.menu_id !== 'cancel'">
                    <n-form-item label="设置菜单名称">
                        <n-input v-model:value="formData.menu_name" placeholder="请输入菜单名称" />
                    </n-form-item>
                </n-gi>
            </n-grid>
        </n-form>
        <div class="button">
            <n-button type="warning" @click="getCurdPreView">查看预览</n-button>
            <n-button type="primary" v-if="curdPreView.controllerPath" @click="submit">
                开始生成
            </n-button>
        </div>
        <div class="ctrl-container" v-if="menuView.path">
            <n-divider>生成菜单预览</n-divider>
            <div class="text-warning">菜单文件路径：{{ menuView.path }}</div>
            <HCode v-model="menuView.menus" language="php" />
        </div>
        <div class="ctrl-container" v-if="curdPreView.controllerPath">
            <n-divider>控制器代码预览</n-divider>
            <div class="text-warning">控制器文件路径：{{ curdPreView.controllerPath }}</div>
            <HCode v-model="curdPreView.controller" language="php" />
        </div>
        <div class="model-container" v-if="curdPreView.modelPath">
            <n-divider>模型代码预览</n-divider>
            <div class="text-warning">模型文件路径：{{ curdPreView.modelPath }}</div>
            <HCode v-model="curdPreView.model" language="php" />
        </div>
        <div class="validate-container" v-if="curdPreView.validatePath">
            <n-divider>验证器代码预览</n-divider>
            <div class="text-warning">验证器文件路径：{{ curdPreView.validatePath }}</div>
            <HCode v-model="curdPreView.validate" language="php" />
        </div>
    </div>
</template>
<script>
export default {
    props: {
        ajaxParams: {}
    },
    data() {
        return {
            plugins: [],
            menus: [],
            menuView: {
                path: '',
                menus: ''
            },
            curdPreView: {
                controllerPath: '',
                modelPath: '',
                validatePath: '',
                controller: '',
                model: '',
                validate: '',
            },
            formData: {
                app_name: '',
                menu_id: '',
                is_cover: 0,
                menu_name: ''
            },
        }
    },
    created() {
        this.initify();
    },
    methods: {
        submit() {
            const _this = this;
            const params = {
                TABLE_NAME: this.ajaxParams.TABLE_NAME,
                app_name: this.formData.app_name,
                is_cover: this.formData.is_cover,
                menu_id: this.formData.menu_id,
                menu_name: this.formData.menu_name
            }
            this.$http.usePost('admin/Curd/add', params).then((res) => {
                _this.$emit("update:closeWin");
                _this.$useNotification?.success({
                    title: res?.msg ?? "操作成功",
                    duration: 1500,
                });
            })
        },
        hanldSelectMenu(value) {
            console.log(value)
            this.formData.menu_id = value;
            if (value === 'cancel') {
                this.formData.menu_name = '';
            }
        },
        hanldSelectApp(value) {
            this.formData.app_name = value;
            this.getPluginMenus(value);
        },
        getCurdPreView() {
            const _this = this;
            const params = {
                TABLE_NAME: this.ajaxParams.TABLE_NAME,
                app_name: this.formData.app_name,
                is_cover: this.formData.is_cover,
                menu_id: this.formData.menu_id,
                menu_name: this.formData.menu_name
            }
            this.$http.useGet('admin/Curd/getPreview', params).then((res) => {
                _this.menuView = res.data?.menus
                _this.curdPreView = res.data?.code;
            })
        },
        getPlugins() {
            const params = {
                TABLE_NAME: this.ajaxParams.TABLE_NAME,
            }
            this.$http.useGet('admin/Curd/detail', params).then((res) => {
                this.plugins = res.data;
            })
        },
        getPluginMenus(app_name) {
            const params = {
                TABLE_NAME: this.ajaxParams.TABLE_NAME,
                app_name: app_name,
            }
            this.$http.useGet('admin/Curd/getPluginMenus', params).then((res) => {
                this.menus = res.data;
            })
        },
        initify() {
            this.getPlugins();
        }
    }
}
</script>
<style lang="scss">
.box-container {
    height: 100%;
    padding: 20px;
    overflow-y: auto;
    overflow-x: hidden;

    .button {
        display: flex;
        justify-content: center;
        gap: 30px;
        margin-top: 30px;
    }
}

.text-warning {
    color: red;
}
</style>