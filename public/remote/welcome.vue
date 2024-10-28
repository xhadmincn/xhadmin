<template>
  <div class="app-container">
    <!-- 任务队列检测 -->
    <!-- <div class="task-queue-container" v-if="taskQueue">
      <div class="task-title">温馨提示</div>
      <pre class="task-queue">{{ taskQueue }}</pre>
    </div> -->
    <!-- 平台应用 -->
    <div class="platform-count">
      <div class="num-container">
        <!-- 平台统计组 -->
        <div class="item">
          <div class="logo-container">
            <img src="/image/welcome/1-wechat.png" class="logo" alt="">
          </div>
          <div class="content">
            <el-statistic title="微信公众号" value-style="font-size:1rem;" :value="platformApp.wechat" />
          </div>
        </div>
        <div class="item">
          <div class="logo-container">
            <img src="/image/welcome/2-wechat-mini.png" class="logo" alt="">
          </div>
          <div class="content">
            <el-statistic title="微信小程序" value-style="font-size:1rem;" :value="platformApp.mini_wechat" />
          </div>
        </div>
        <div class="item">
          <div class="logo-container">
            <img src="/image/welcome/3-douyin.png" class="logo" alt="">
          </div>
          <div class="content">
            <el-statistic title="抖音小程序" value-style="font-size:1rem;" :value="platformApp.douyin" />
          </div>
        </div>
        <div class="item">
          <div class="logo-container">
            <img src="/image/welcome/4-h5.png" class="logo" alt="">
          </div>
          <div class="content">
            <el-statistic title="网页应用" value-style="font-size:1rem;" :value="platformApp.h5" />
          </div>
        </div>
        <div class="item">
          <div class="logo-container">
            <img src="/image/welcome/5-app.png" class="logo" alt="">
          </div>
          <div class="content">
            <el-statistic title="APP应用" value-style="font-size:1rem;" :value="platformApp.app" />
          </div>
        </div>
        <div class="item">
          <div class="logo-container">
            <img src="/image/welcome/6-other.png" class="logo" alt="">
          </div>
          <div class="content">
            <el-statistic title="其他应用" value-style="font-size:1rem;" :value="platformApp.other" />
          </div>
        </div>
      </div>
    </div>
    <!-- 请求数据 -->
    <div class="http-container">
      <div class="echart-container">
        <div class="echart" ref="httpRef"></div>
      </div>
      <div class="team-product">
        <div class="item">
          <div class="title">开发团队</div>
          <el-table :data="teamTable" border stripe style="width: 100%">
            <el-table-column prop="title" label="名称" width="100" />
            <el-table-column prop="values" label="数据">
              <template #default="scope">
                <div v-html="scope.row.values" />
              </template>
            </el-table-column>
          </el-table>
        </div>
        <div class="item">
          <div class="title">产品动态</div>
          <el-table :data="productTable.data" border stripe style="width: 100%">
            <el-table-column prop="title" label="名称" width="100" />
            <el-table-column prop="values" label="数据">
              <template #default="scope">
                <div v-html="scope.row.values" />
              </template>
            </el-table-column>
          </el-table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      taskQueue:'',
      platformApp: {
        wechat: 0,
        mini_wechat: 0,
        douyin: 0,
        h5: 0,
        app:0,
        other: 0,
      },
      // 团队
      teamTable: [],
      // 产品动态
      productTable: {
        columns: [
          {
            title: '标题',
            key: 'title',
          },
          {
            title: '发布',
            width: 150,
            key: 'values',
          },
        ],
        data: [],
      },
      // 请求访问
      httpOptions: {
        title: {
          text: '应用增涨'
        },
        tooltip: {
          trigger: 'axis'
        },
        legend: {
          data: [
            '微信公众号',
            '微信小程序',
            '抖音小程序',
            '网页应用',
            'APP应用',
            '其他应用',
          ]
        },
        grid: {
          left: '3%',
          right: '4%',
          bottom: '3%',
          containLabel: true
        },
        xAxis: {
          type: 'category',
          boundaryGap: false,
          data: ['今日', '本周', '本月', '季度内', '半年内', '今年']
        },
        yAxis: {
          type: 'value'
        },
        series: []
      }
    };
  },
  mounted() {
    this.initify();
  },
  methods: {
    // 处理团队渲染规则
    checkTeamRule(data) {
      return data.map((item) => {
        if (item.values instanceof Array) {
          let html = [];
          for (let index = 0; index < item.values.length; index++) {
            const element = item.values[index];
            html.push(`<a href="${element.url}" class="a-link" target="_blank">${element.name}</a>`)
          }
          item.values = html.join('，');
        }
        return item
      })
    },
    initify() {
      const _this = this;
      _this.$http.useGet('admin/Index/consoleCount').then((res) => {
        const { data } = res;
        _this.platformApp = data.platformApp
        _this.taskQueue = data?.taskQueue ?? ''
        _this.teamTable = _this.checkTeamRule(data.team)
        _this.httpOptions.series = data.platform_echarts
        _this.echats = this.$echarts.init(this.$refs.httpRef);
        _this.echats.setOption(this.httpOptions);
      })
    },
  },
};
</script>

<style lang="scss" scoped>
.app-container {
  height: 100%;

  .task-queue-container{
    background: #fff;
    padding:10px 20px;
    margin-bottom: 10px;
    border-radius: 4px;
    .task-title{
      font-size:14px;
    }
    .task-queue{
      margin:0;
      color:red;
      font-size:12px;
      line-height:26px;
    }
  }
  .platform-count {
    padding: 0 0 10px 0;

    .num-container {
      display: flex;
      gap: 20px;

      .item {
        width: 16.666%;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 5px;
        background: #fff;

        .logo-container {
          width: 100px;
          height: 80px;
          display: flex;
          justify-content: right;
          align-items: center;

          .logo {
            width: 80px;
            height: 80px;
            user-select: none;
            pointer-events: none;
          }
        }

        .content {
          flex: 1;
          padding: 25px 0;

          .el-statistic {
            .el-statistic__head {
              display: flex;
              justify-content: center;
              font-size: 16px;
            }

            .el-statistic__content {
              display: flex;
              justify-content: center;
            }
          }
        }
      }
    }
  }

  .http-container {
    margin-top: 10px;
    display: flex;
    gap: 30px;

    .echart-container {
      flex: 1;
      padding: 20px;
      background: #fff;

      .echart {
        width: 100%;
        height: 600px;
      }
    }

    .team-product {
      width: 500px;
      display: flex;
      flex-direction: column;
      gap: 30px;
      background: #fff;
      padding: 10px;

      .item {
        .title {
          font-size: 20px;
          font-weight: 700;
          margin-bottom: 10px;
        }
      }
    }
  }
}

.a-link {
  color: #555;
}

.a-link:hover {
  color: var(--xh-color-primary);
}</style>
