<template>
	<div class="h-100% w-100% app-container">
		<div>
			<el-button type="primary" @click="showDialog">选择已安装应用插件</el-button>
		</div>
		<!-- 已授权列表 -->
		<div class="app-list">
			<div class="item" v-for="(item, index) in plugins" :key="index">
				<div class="auth-info">
					<img :src="item.logo ?? ''" class="logo" alt="">
					<div class="title">{{ item.title }}</div>
					<div class="auth-num">
						授权数：<span>{{ item.auth_num }}</span>，
						剩余：<span>{{ item.stock_auth_num }}</span>
					</div>
					<div class="auth-num">已授权数：<span>{{ item.use_auth_num }}</span></div>
					<div>
						<el-button type="danger" size="small" @click="cannelAuth(index)">取消所有授权</el-button>
						<el-button type="success" size="small" @click="addAuth(index)">授权</el-button>
					</div>
				</div>
				<div class="flex-1 auth-list">
					<div class="app-item" v-for="(formItem, i) in item.form">
						<el-input-number v-model="formItem.auth_num" :min="0" class="auth-number" />
						<el-date-picker v-model="formItem.expire_time" type="date" placeholder="有效期" format="YYYY-MM-DD"
							value-format="YYYY-MM-DD" :shortcuts="expireTimeShortcuts" />
						<el-button type="danger" size="small" @click="cannelSubAuth(index, i)">取消授权</el-button>
					</div>
				</div>
			</div>
		</div>
		<!-- 选择组件弹窗 -->
		<el-dialog v-model="modalDialog" title="选择已安装应用插件" width="50vw">
			<div class="app-select-list">
				<div class="item" v-for="(item, index) in plugin_list" :key="index">
					<div class="app-item" :class="{ 'active': selected.includes(item.name) }" @click="hanldSelect(item)">
						<img :src="item?.logo ?? ''" class="logo" alt="" />
						<div class="title">{{ item?.title ?? '错误' }}</div>
					</div>
				</div>
			</div>
		</el-dialog>
	</div>
</template>

<script>
export default {
	props: {
		modelValue: Object,
		plugin_list: Array,
	},
	data() {
		return {
			plugins: [],
			selected: [],
			modalDialog: false,
			expireTimeShortcuts: [
				{
					text: '三天试用',
					value() {
						const date = new Date()
						date.setDate(date.getDate() + 3)
						return date
					}
				},
				{
					text: '一个月',
					value() {
						const date = new Date()
						date.setMonth(date.getMonth() + 1)
						return date
					}
				},
				{
					text: '三个月',
					value() {
						const date = new Date()
						date.setMonth(date.getMonth() + 3)
						return date
					}
				},
				{
					text: '半年',
					value() {
						const date = new Date()
						date.setMonth(date.getMonth() + 6)
						return date
					}
				},
				{
					text: '一年',
					value() {
						const date = new Date()
						date.setFullYear(date.getFullYear() + 1)
						return date
					}
				},
				{
					text: '两年半',
					value() {
						const date = new Date()
						date.setTime(date.getTime() + 3600 * 1000 * 24 * 365 * 2.5)
						return date
					}
				},
				{
					text: '三年',
					value() {
						const date = new Date()
						date.setFullYear(date.getFullYear() + 3)
						return date
					}
				},
				{
					text: '五年',
					value() {
						const date = new Date()
						date.setFullYear(date.getFullYear() + 5)
						return date
					}
				},
				{
					text: '十年',
					value() {
						const date = new Date()
						date.setFullYear(date.getFullYear() + 10)
						return date
					}
				}
			]
		}
	},
	watch: {
		modelValue: {
			handler(val) {
				this.plugins = val;
			},
			deep: true,
		},
		plugins: {
			handler(val) {
				console.log(val);
				this.$emit('update:modelValue', val);
			},
			deep: true,
		}
	},
	mounted() {
		this.plugins = this.modelValue;
	},
	methods: {
		showDialog() {
			this.selected = this.plugins.map(item => item.name);
			this.modalDialog = true;
		},
		hanldSelect(item) {
			if (!this.selected.includes(item.name)) {
				this.selected.push(item.name);
				let value = this.plugins;
				value.push({
					name: item.name,
					title: item.title,
					logo: item.logo,
					auth_num: item.auth_num,
					use_auth_num: item.use_auth_num,
					stock_auth_num: item.auth_num - item.use_auth_num,
					form: []
				});
			}
		},
		cannelAuth(index) {
			const f = this.plugins[index];
			if (f && f.id) {
				this.$useConfirm('确定取消授权吗？', '温馨提示', 'warning').then(() => {
					this.$http.usePost('admin/StoreApp/cannelAuth', {
						id: f.id
					}).then((res) => {
						window.location.reload();
					})
				})
			} else {
				this.plugins.splice(index, 1);
			}
		},
		cannelSubAuth(index, i) {
			const f = this.plugins[index];
			if (!f) {
				return;
			}
			const form = f.form[i];
			if (form && form.id) {
				this.$useConfirm('确定取消授权吗？', '温馨提示', 'warning').then(() => {
					this.$http.usePost('admin/StoreApp/cannelSubAuth', {
						id: form.id
					}).then((res) => {
						window.location.reload();
					})
				})
			} else {
				f.form.splice(i, 1);
			}
		},
		addAuth(index) {
			if (!this.plugins[index]) {
				return;
			}
			if (!this.plugins[index].form) {
				this.plugins[index].form = [];
			}
			this.plugins[index].form.push({
				auth_num: 0,
				expire_time: ''
			});
		}
	},
}
</script>

<style lang="scss" scoped>
.app-container {
	display: flex;
	flex-direction: column;
	background: #fff;

	.app-list {
		flex: 1;
		display: flex;
		flex-direction: column;
		padding: 10px 0;
		gap: 10px;

		.item {
			display: flex;
			width: 100%;
			box-shadow: var(--el-box-shadow);
			border-radius: 10px;

			.auth-info {
				width: 200px;
				display: flex;
				flex-direction: column;
				// justify-content: center;
				align-items: center;
				padding: 20px 10px;
				line-height: 1;
				gap: 5px;

				.logo {
					width: 80px;
					height: 80px;
					border-radius: 5px;
					margin-bottom: 10px;
				}

				.title {
					font-size: 14px;
					line-height: 20px;
					overflow: hidden;
					text-overflow: ellipsis;
					display: -webkit-box;
					-webkit-line-clamp: 1;
					-webkit-box-orient: vertical;
					font-weight: 600;
				}

				.auth-num {
					font-size: 12px;
					line-height: 20px;

					span {
						color: var(--el-color-danger);
					}
				}
			}

			.auth-list {
				display: flex;
				flex-wrap: wrap;
				padding: 20px;
				gap: 10px;
				justify-content: flex-start;
				align-items: flex-start;

				.auth-number {
					width: 100%;
				}

				.app-item {
					width: 160px;
					border-radius: 5px;
					background-color: var(--el-bg-color-page);
					display: flex;
					flex-direction: column;
					justify-content: center;
					align-items: center;
					padding: 10px;
					gap: 10px;
					cursor: pointer;
				}

				.app-item:hover {
					background: var(--el-color-primary-light-9);
				}
			}
		}

	}
}

.app-dialog {
	width: 45%;
	padding: 0;

	.n-dialog__title {
		padding: 10px 20px;
		border-bottom: 1px solid #d5d5d5;
	}

	.n-dialog__content {
		border-bottom: 1px solid #d5d5d5;
	}

	.n-dialog__action {
		padding: 0 0 15px 0;
		justify-content: center;
	}
}

.app-select-list {
	padding: 3vh;
	display: flex;
	flex-wrap: wrap;
	gap: 3vh;
	line-height: 1;

	.item {
		width: 120px;
		height: 120px;

		.app-item {
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
			border: 1px solid #d5d5d5;
			cursor: pointer;
			border-radius: 5px;
			width: 120px;
			height: 120px;

			.logo {
				width: 100%;
				height: 100px;
				border-top-left-radius: 5px;
				border-top-right-radius: 5px;
			}

			.title {
				font-size: 12px;
				line-height: 20px;
				overflow: hidden;
				text-overflow: ellipsis;
				display: -webkit-box;
				-webkit-line-clamp: 1;
				-webkit-box-orient: vertical;
			}
		}

		.active,
		.app-item:hover {
			border: 1px solid red;
		}
	}
}

.flex-1 {
	flex: 1;
}
</style>