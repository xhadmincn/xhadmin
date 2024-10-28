<template>
    <div class="app-container">
        <div class="project-container">
            <div class="category">
                <div class="tabs">
                    <el-tabs :model-value="projects.platformActive" @tab-change="hanldTabs">
                        <el-tab-pane :label="item.label" :name="item.key" v-for="(item, index) in platforms"
                            :key="index" />
                    </el-tabs>
                </div>
                <div class="created">
                    <el-button type="primary" class="develop-btn" color="#626aef" v-if="isDeveloper"
                        @click="hanldOepn('/Develop/create', { isBack: 1 })">
                        创建开发者项目
                    </el-button>
                    <el-button type="primary" @click="hanldOepn('/StoreApp/create', { isBack: 1 })">
                        新增项目
                    </el-button>
                    <el-button type="primary" bg text @click="switchListMode">
                        <template #icon>
                            <svg t=" 1712658184007" v-if="listMode==='list'" viewBox="0 0 1024 1024" version="1.1"
                                xmlns="http://www.w3.org/2000/svg" p-id="8072" id="mx_n_1712658184010" width="64"
                                height="64">
                                <path d="M872.802928 755.99406 872.864326 755.99406 872.864326 755.624646Z"
                                    fill="var(--el-button-text-color)" p-id="8073"></path>
                                <path
                                    d="M349.405343 154.62259 155.596266 154.62259c-29.150924 0-52.873208 23.724331-52.873208 52.845579l0 193.823404c0 29.150924 23.722284 52.844555 52.873208 52.844555l193.810101 0c29.136597 0 52.859905-23.693632 52.859905-52.844555L402.266271 207.467145C402.265248 178.345897 378.541941 154.62259 349.405343 154.62259M351.851045 207.467145l0 193.823404c0 1.339508-1.106194 2.474354-2.445702 2.474354L155.596266 403.764903c-1.338485 0-2.503007-1.134847-2.503007-2.474354L153.093259 207.467145c0-1.38351 1.134847-2.474354 2.503007-2.474354l193.810101 0C350.774527 204.992791 351.851045 206.084659 351.851045 207.467145"
                                    fill="var(--el-button-text-color)" p-id="8074"></path>
                                <path
                                    d="M349.405343 569.837266 155.596266 569.837266c-29.150924 0-52.873208 23.75196-52.873208 52.856835L102.723058 816.519552c0 29.164227 23.722284 52.857858 52.873208 52.857858l193.810101 0c29.136597 0 52.859905-23.693632 52.859905-52.857858L402.266271 622.694101C402.265248 593.589227 378.541941 569.837266 349.405343 569.837266M351.851045 622.694101 351.851045 816.519552c0 1.36816-1.106194 2.472308-2.445702 2.472308L155.596266 818.99186c-1.338485 0-2.503007-1.104147-2.503007-2.472308L153.093259 622.694101c0-1.367137 1.134847-2.474354 2.503007-2.474354l193.810101 0C350.774527 620.219747 351.851045 621.326964 351.851045 622.694101"
                                    fill="var(--el-button-text-color)" p-id="8075"></path>
                                <path
                                    d="M519.857457 224.930889c0.727571 0.480954 1.601474 0.801249 2.532683 0.801249l1.77646 0c0.434905 0.058328 0.900509 0.058328 1.36816 0l367.768061 0c0.233314 0.029676 0.493234 0.058328 0.697895 0.058328 0.233314 0 0.466628-0.028653 0.670266-0.058328l1.454118 0c0.787946 0 1.514493-0.261966 2.124384-0.626263 12.023848-2.081405 20.900006-12.341073 20.900006-24.464181 0-11.949146-8.586562-22.064528-20.345374-24.348548-0.756223-0.566912-1.687432-0.931209-2.679016-0.931209L522.389116 175.361937c-0.901533 0-1.775436 0.422625-2.501984 1.019213-11.614525 2.357697-20.200064 12.458753-20.200064 24.261567C499.687068 212.474183 508.272607 222.574215 519.857457 224.930889"
                                    fill="var(--el-button-text-color)" p-id="8076"></path>
                                <path
                                    d="M519.507486 411.493935c0.756223 0.683569 1.77646 1.134847 2.88163 1.134847l373.73496 0c0.903579 0 1.719154-0.39295 2.446725-0.932232 11.818163-2.196015 20.550035-12.369725 20.550035-24.405853 0-12.123109-8.847505-22.354124-20.784372-24.434505-0.668219-0.39295-1.425466-0.596588-2.212388-0.596588l-0.697895 0.115634c-0.581238-0.145309-1.456165-0.203638-2.297322-0.115634l-366.867552 0.115634c-0.611937-0.145309-1.457188-0.203638-2.300392-0.115634l-1.571798 0c-0.814552 0-1.542122 0.233314-2.213411 0.714268-11.786441 2.241041-20.459984 12.398378-20.459984 24.317848C499.715721 398.976854 508.040317 408.990928 519.507486 411.493935"
                                    fill="var(--el-button-text-color)" p-id="8077"></path>
                                <path
                                    d="M519.537162 660.680251c0.756223 0.667196 1.775436 1.075495 2.851954 1.075495l373.73496 0c0.408299 0 0.787946-0.058328 1.196245-0.174985 13.271258-0.608867 23.954575-11.641131 23.954575-25.118074 0-13.827937-11.294231-25.118074-25.15082-25.118074-0.202615 0-0.435928 0.027629-0.697895 0.058328L525.50406 611.402941c-0.437975-0.058328-0.873903-0.058328-1.36816 0l-1.746784 0c-0.842181 0-1.600451 0.25992-2.270716 0.725524-11.729136 2.270716-20.402679 12.428054-20.402679 24.334221C499.715721 648.166239 508.097622 658.176221 519.537162 660.680251"
                                    fill="var(--el-button-text-color)" p-id="8078"></path>
                                <path
                                    d="M897.755226 777.807878c-0.493234-0.203638-1.077541-0.319272-1.63115-0.319272L522.389116 777.488607c-0.959861 0-1.893116 0.462534-2.589988 1.1328-11.583826 2.416026-20.083407 12.485359-20.083407 24.275893 0 11.904121 8.674567 22.064528 20.402679 24.334221 0.669242 0.405229 1.428535 0.60989 2.270716 0.60989l0.728594-0.115634c0.290619 0.057305 0.726547 0.202615 2.620687 0.115634l368.639918-0.115634c0.466628 0.115634 1.340531 0.290619 1.74576 0.290619 13.856589 0 25.15082-11.291161 25.15082-25.119097C921.274896 789.565666 910.883221 778.679735 897.755226 777.807878"
                                    fill="var(--el-button-text-color)" p-id="8079"></path>
                            </svg>
                            <svg t="1712658534185" class="icon" viewBox="0 0 1024 1024" version="1.1"
                                xmlns="http://www.w3.org/2000/svg" p-id="8841" width="256" height="256" v-else>
                                <path d="M872.802928 755.99406 872.864326 755.99406 872.864326 755.624646Z"
                                    fill="var(--el-button-text-color)" p-id="8842"></path>
                                <path
                                    d="M400.807037 542.518061c-0.588401-0.224104-1.233084-0.364297-1.876744-0.364297L197.800483 542.153764c-44.358277 0-80.713276 35.83516-81.231069 79.999009-0.026606 0.195451-0.026606 0.363274-0.026606 0.561795l0 189.938933c0 2.550079 0.432859 5.158487 1.176802 7.264451 6.688329 39.057551 40.33566 67.432809 80.080873 67.432809L398.92927 887.35076c0.588401 0 1.12052-0.11154 1.653663-0.336668 37.179784-2.863211 67.295686-30.312376 73.294306-66.283636 1.011027-2.7486 1.458211-5.499248 1.458211-8.131191l0-189.884698c0-0.197498 0-0.36532 0-0.534166C474.833007 580.541049 442.362478 545.94102 400.807037 542.518061M425.259962 627.142413l0 175.219697c0 19.346627-15.787568 35.051307-35.219129 35.051307L208.764186 837.413418c-0.335644-0.059352-0.715291-0.059352-1.066285 0l-5.872754 0c-19.390629 0-35.177174-15.70468-35.177174-35.051307L166.647973 627.142413c0-19.292392 15.786545-35.020608 35.177174-35.020608l188.214662 0C409.472394 592.121805 425.259962 607.850022 425.259962 627.142413"
                                    fill="var(--el-button-text-color)" p-id="8843"></path>
                                <path
                                    d="M892.595716 191.294814c-0.476861-41.625026-32.973996-76.227102-74.501808-79.646991-0.61603-0.223081-1.234107-0.363274-1.904373-0.363274L636.370077 111.284549c-44.556798 0-81.035618 36.114522-81.260745 80.556711l0 189.94098c0 2.636037 0.449231 5.341658 1.179872 7.262404 6.67298 39.046295 40.289612 67.421552 80.080873 67.421552l179.819458 0c0.588401 0 1.121544-0.11154 1.65264-0.322341 37.154201-2.873444 67.323315-30.325679 73.351612-66.300009 0.953721-2.691295 1.401929-5.426593 1.401929-8.118911l0-189.884698C892.595716 191.644785 892.595716 191.475939 892.595716 191.294814M842.516134 196.270129l0 175.206394c0 19.348674-15.785521 35.049261-35.160801 35.049261L640.349715 406.525784c-19.374256 0-35.160801-15.70161-35.160801-35.049261L605.188914 196.270129c0-19.303648 15.787568-35.034934 35.160801-35.034934l167.005618 0C826.730613 161.235195 842.516134 176.966481 842.516134 196.270129"
                                    fill="var(--el-button-text-color)" p-id="8844"></path>
                                <path
                                    d="M818.093908 542.518061c-0.61603-0.224104-1.234107-0.364297-1.904373-0.364297L636.370077 542.153764c-44.556798 0-81.035618 36.116569-81.260745 80.560804l0 189.938933c0 2.636037 0.449231 5.353938 1.179872 7.264451 6.67298 39.057551 40.289612 67.432809 80.080873 67.432809l179.819458 0c0.588401 0 1.121544-0.11154 1.65264-0.336668 37.154201-2.863211 67.323315-30.312376 73.351612-66.283636 0.953721-2.693342 1.401929-5.439896 1.401929-8.131191l0-189.884698c0-0.197498 0-0.36532 0-0.534166C892.118855 580.541049 859.62172 545.94102 818.093908 542.518061M842.516134 627.142413l0 175.219697c0 19.346627-15.785521 35.051307-35.160801 35.051307L640.349715 837.413418c-19.374256 0-35.160801-15.70468-35.160801-35.051307L605.188914 627.142413c0-19.292392 15.787568-34.991955 35.160801-34.991955l167.005618 0C826.730613 592.149435 842.516134 607.850022 842.516134 627.142413"
                                    fill="var(--el-button-text-color)" p-id="8845"></path>
                                <path
                                    d="M400.807037 111.646799c-0.588401-0.223081-1.233084-0.363274-1.876744-0.363274L197.800483 111.283526c-44.358277 0-80.713276 35.83516-81.231069 79.983659-0.026606 0.209778-0.026606 0.3776-0.026606 0.573051l0 189.94098c0 2.553149 0.432859 5.160533 1.176802 7.262404 6.688329 39.046295 40.33566 67.421552 80.080873 67.421552L398.92927 456.465173c0.588401 0 1.12052-0.11154 1.653663-0.322341 37.179784-2.873444 67.295686-30.325679 73.294306-66.300009 1.011027-2.803859 1.458211-5.454222 1.458211-8.118911l0-189.884698c0-0.195451 0-0.363274 0-0.545422C474.833007 149.669788 442.362478 115.067712 400.807037 111.646799M425.259962 196.270129l0 175.206394c0 19.348674-15.787568 35.049261-35.219129 35.049261L201.825147 406.525784c-19.390629 0-35.177174-15.70161-35.177174-35.049261L166.647973 196.270129c0-19.303648 15.786545-35.034934 35.177174-35.034934l188.214662 0C409.472394 161.235195 425.259962 176.966481 425.259962 196.270129"
                                    fill="var(--el-button-text-color)" p-id="8846"></path>
                            </svg>
                        </template>
                    </el-button>
                </div>
            </div>
            <div class="xh-project" v-if="projects.list.length">
                <div class="project-list" :class="['project-list-'+listMode]">
                    <div class="item" v-for="(item, index) in projects.list" :key="index">
                        <div class="project-info pointer" @click="hanldAdmin(item)" title="进入应用后台">
                            <el-avatar class="logo" :src="item.logo" shape="square">
                                {{ item.title }}
                            </el-avatar>
                            <div class="flex-1">
                                <div class="title">
                                    {{ item.title }}
                                </div>
                                <div class="tags">
                                    {{ item.platform_text }}
                                </div>
                                <div class="auth" :class="[item.auth_class ? item.auth_class : '']">
                                    {{ item.auth_text }}
                                </div>
                            </div>
                        </div>
                        <div class="site-info">
                            <div>超管账号：{{item.username}}</div>
                            <div>站点ID：{{item.id}}</div>
                        </div>
                        <div class="btns">
                            <el-popconfirm title="是否确定将该项目放入回收站？" @confirm="actionDelProject(item)" width="300">
                                <template #reference>
                                    <el-button type="danger" size="small" bg text>
                                        删除
                                    </el-button>
                                </template>
                            </el-popconfirm>
                            <div class="flex-1"></div>
                            <el-button type="primary" size="small"
                                @click="hanldOepn('/StoreApp/edit', { id: item.id, isBack: 1 })">
                                编辑
                            </el-button>
                            <el-dropdown split-button type="primary" size="small" @click="hanldAdmin(item)"
                                @command="hanldAdminClick($event,item)" placement="bottom-end">
                                进入
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item command="copy-login-url">复制登录链接</el-dropdown-item>
                                    </el-dropdown-menu>
                                </template>
                            </el-dropdown>
                        </div>
                    </div>
                </div>
                <div class="xh-paginate">
                    <el-pagination background layout="prev, pager, next" v-model:current-page="projects.paginate.page"
                        :total="projects.paginate.total" :page-size="projects.paginate.limit"
                        @current-change="hanldChangePage" />
                </div>
            </div>
            <div class="project-empty" v-else>
                <el-empty description="当前没有更多的项目" />
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                isDeveloper: false,
                listMode: 'grid',
                user: {},
                platformApp: [],
                platforms: [
                    {
                        label: '全部项目',
                        key: ''
                    },
                ],
                projects: {
                    platformActive: '',
                    paltformType: {
                        'wechat': 'success',
                        'mini_wechat': 'success',
                        'douyin': 'warning',
                        'h5': 'danger',
                        'app': '',
                        'other': 'info',
                    },
                    paginate: {
                        page: 1,
                        limit: 8,
                        total: 0,
                    },
                    list: [],
                },
            }
        },
        mounted() {
            this.listMode = window.localStorage.getItem('listMode') || 'grid';
            this.user = this.$userApp.userInfo;
            this.getIndex();
            this.getList();
        },
        methods: {
            switchListMode() {
                this.listMode = this.listMode === 'list' ? 'grid' : 'list';
                window.localStorage.setItem('listMode', this.listMode);
            },
            // 执行删除项目
            actionDelProject(e) {
                const _this = this;
                const { id } = e;
                _this.$http.useDelete('store/StoreApp/del', { id }).then((res) => {
                    _this.platformActive = ''
                    _this.getIndex();
                    _this.getList();
                    _this.$useNotify(res?.msg ?? "操作成功", 'success', '温馨提示')
                })
            },
            // 删除项目
            hanldDel(e) {
                const _this = this;
                _this.$useConfirm('是否确定将该项目放入回收站？', '温馨提示', 'error').then(() => {
                    _this.actionDelProject(e);
                })
            },
            // 跳转项目后台
            hanldAdmin(e, callback) {
                const _this = this;
                const params = {
                    appid_id: e.id,
                }
                _this.$http.usePost('store/StoreApp/login', params).then((res) => {
                    if (res.code === 200) {
                        const { data } = res;
                        if (data?.url) {
                            if (typeof callback == "function") {
                                callback(data)
                            } else {
                                window.open(data?.url)
                            }
                        } else {
                            _this.$useNotify('登录项目管理失败', 'error', '温馨提示')
                        }
                    } else {
                        _this.$useNotify('操作数据失败', 'error', '温馨提示')
                    }
                }).catch((err) => {
                    _this.$useNotify(err?.msg ?? '操作数据失败', 'error', '温馨提示')
                })
            },
            hanldAdminClick(command, e) {
                console.log(command, e);
                switch (command) {
                    case 'copy-login-url':
                        this.hanldAdmin(e, (data) => {
                            this.setClipboard(window.location.origin + data?.url)
                        });
                        break;
                }
            },
            setClipboard(text) {
                if (navigator.clipboard && globalThis.isSecureContext) {
                    navigator.clipboard.writeText(text).then(_ => {
                        this.$useMessage('已复制', 'success');
                    })
                } else {
                    let input = document.createElement('input');
                    input.value = text;
                    document.body.appendChild(input);
                    input.select();
                    document.execCommand('Copy');
                    input.remove();
                    this.$useMessage('已复制', 'success');
                }
            },
            // 跳转页面
            hanldOepn(path, item = {}) {
                this.$routerApp.push({
                    path: path,
                    query: item
                });
            },
            hanldChangePage(value) {
                this.projects.paginate.page = value;
                this.getList();
            },
            hanldTabs(name) {
                this.projects.platformActive = name;
                this.getList();
            },
            // 获取项目列表
            getList() {
                const _this = this;
                const params = {
                    platform: _this.projects.platformActive,
                    page: _this.projects.paginate.page,
                    limit: _this.projects.paginate.limit,
                }
                _this.$http.useGet('store/StoreApp/index', params).then((res) => {
                    // 列表
                    _this.projects.list = res?.data?.data ?? [];
                    // 分页
                    _this.projects.paginate.total = res?.data?.total ?? 0;
                    _this.projects.paginate.page = res?.data?.current_page ?? 1;
                    _this.projects.paginate.limit = res?.data?.per_page ?? 10;
                })
            },
            // 获取首页数据
            getIndex() {
                const _this = this;
                _this.platforms = [
                    {
                        label: '全部项目',
                        key: ''
                    },
                ];
                _this.$http.useGet('store/Index/indexData').then((res) => {
                    const { data } = res;
                    const { platformApp, isDeveloper } = data;
                    _this.isDeveloper = isDeveloper;
                    _this.platformApp = platformApp
                    _this.platforms.push(...platformApp);
                })
            },
        }
    };
</script>

<style lang="scss">
    .app-container {
        height: 100%;
        display: flex;
        flex-direction: column;
        box-sizing: border-box;

        .user-container {
            background-color: #fff;
            border-radius: 5px;
            padding: 15px;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;

            .info {
                display: flex;
                flex-direction: row;
                align-items: center;

                .nickname {
                    margin-left: 10px;

                    .title {
                        font-weight: bolder;
                    }

                    .date {
                        font-size: 12px;
                        color: #606266;
                    }
                }
            }

            .platform {
                display: flex;
                flex-direction: row;
                align-items: center;

                .item {
                    display: flex;
                    flex-direction: row;
                    align-items: center;
                    margin: 10px 0px;
                    justify-content: center;

                    .icon {
                        padding-top: 15px;
                    }

                    .content {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        margin: 0px 10px;

                        .number {
                            color: #252631;
                            font-size: 20px
                        }

                        .title {
                            color: #98a9bc;
                            font-size: 12px
                        }
                    }
                }
            }
        }

        .project-container {
            flex: 1;

            .category {
                display: flex;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                margin: 15px 0px;
                background-color: var(--el-bg-color);
                padding: 10px;
                border-radius: var(--el-border-radius-base);

                .created {
                    .develop-btn {
                        margin-right: 10px;
                    }
                }
            }

            .xh-project {
                background-color: var(--el-bg-color);
                padding: 20px;
                border-radius: var(--el-border-radius-base);

                .project-list-grid {
                    display: grid;
                    grid-template-columns: repeat(24, minmax(0px, 1fr));
                    gap: 20px;
                    align-items: flex-start;
                    align-content: flex-start;

                    .item {
                        position: relative;
                        background-color: #fff;
                        grid-column: span 6 / span 6;
                        display: flex;
                        flex-direction: column;
                        border-radius: var(--el-border-radius-base);
                        overflow: hidden;
                        transition: all .3s ease;
                        box-shadow: var(--el-box-shadow-lighter);

                        .project-info {
                            display: flex;
                            align-items: center;
                            padding: 10px;

                            .logo {
                                --el-avatar-size: 76px;
                            }

                            .flex-1 {
                                padding-left: 10px;
                            }
                        }

                        .btns {
                            display: flex;
                            padding: 10px;
                            gap: 10px;
                        }

                        .title {
                            color: var(--el-text-color-primary);
                            margin-bottom: 10px;
                            font-weight: bold;
                        }

                        .tags {
                            font-size: 12px;
                            margin-bottom: 10px;
                            color: var(--el-text-color-secondary);
                        }

                        .auth {
                            font-size: 12px;
                            color: var(--el-color-success);
                        }

                        .auth.auth-not {
                            color: var(--el-text-color-secondary);
                        }

                        .auth.auth-expire {
                            color: var(--el-color-error);
                        }

                        .site-info {
                            margin: 10px;
                            padding: 10px 0;
                            font-size: 12px;
                            color: var(--el-text-color-secondary);
                            display: flex;
                            justify-content: space-between;
                            gap: 10px;
                            border-top: solid 1px #F5F5F5;
                            border-bottom: solid 1px #F5F5F5;
                        }
                    }

                    .item:hover {
                        box-shadow: var(--el-box-shadow);
                    }
                }

                .project-list-list {
                    display: grid;
                    grid-template-columns: repeat(24, minmax(0px, 1fr));
                    gap: 20px;
                    align-items: flex-start;
                    align-content: flex-start;

                    .item {
                        position: relative;
                        background-color: #fff;
                        grid-column: span 24 / span 24;
                        display: flex;
                        flex-direction: row;
                        border-radius: var(--el-border-radius-base);
                        overflow: hidden;
                        transition: all .3s ease;
                        box-shadow: var(--el-box-shadow-lighter);
                        align-items: center;

                        .project-info {
                            flex: 1;
                            display: flex;
                            align-items: center;
                            padding: 10px;

                            .logo {
                                --el-avatar-size: 76px;
                            }

                            .flex-1 {
                                display: flex;
                                flex-direction: row;
                                padding-left: 10px;
                                align-items: center;
                            }
                        }

                        .btns {
                            display: flex;
                            padding: 10px;
                            gap: 10px;
                        }

                        .title {
                            color: var(--el-text-color-primary);
                            font-weight: bold;
                            flex: 1;
                            display: flex;
                            align-items: center;
                        }

                        .tags {
                            font-size: 12px;
                            color: var(--el-text-color-secondary);
                            width: 200px;
                        }

                        .auth {
                            font-size: 12px;
                            color: var(--el-color-success);
                            width: 180px;
                        }

                        .auth.auth-not {
                            color: var(--el-text-color-secondary);
                        }

                        .auth.auth-expire {
                            color: var(--el-color-error);
                        }

                        .site-info {
                            padding: 10px;
                            font-size: 12px;
                            color: var(--el-text-color-secondary);
                            display: flex;
                            justify-content: space-between;
                            gap: 20px;
                        }
                    }

                    .item:hover {
                        box-shadow: var(--el-box-shadow);
                    }
                }

                .xh-paginate {
                    padding: 20px;
                    display: flex;
                    flex-direction: row;
                    align-items: center;
                    justify-content: center;
                }
            }

            .project-empty {
                padding: 100px 0;
                border-radius: 8px;
            }
        }
    }
    .pointer{
        cursor: pointer;
    }
</style>