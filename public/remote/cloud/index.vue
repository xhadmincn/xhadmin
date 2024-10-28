<template>
  <div class="cloud-container">
    <div class="bill-container">
      <div class="bill-title">账单流水</div>
      <div class="bill-list" ref="billListRef">
        <div class="item" v-for="(item,index) in datalist" :key="index">
          <!-- <div class="order_no">订单编号：dsadsadas</div> -->
          <div class="info-container">
          <img src="/image/bill-icon.png" class="bill-icon" alt="">
            <div class="info">
              <div class="title">{{ item.remarks }}</div>
              <div class="date">账单时间：{{ item.create_at }}</div>
            </div>
            <div class="price" :class="item.bill_type === 1 ? 'inc' : 'dec'">
            {{item.bill_type === 1 ? '+' : '-'}}￥{{ item.money }}
            </div>
          </div>
        </div>
        <div ref="bottomRef"></div>
      </div>
    </div>
    <div class="user-container">
      <div class="user-block">
        <div class="user-title">个人中心</div>
        <div class="user">
          <img :src="user?.headimg" class="headimg" />
          <div class="nickname">{{ user?.nickname ?? '未设置昵称' }}</div>
          <div class="username">{{ user?.username ?? '未设置账号' }}</div>
          <div class="last-date">最后登录 {{ user?.last_login_time }}</div>
        </div>
        <div class="tools">
          <div class="item" @click="hanldOpen('/#/control/user')">
            <img src="/image/personal.png" class="icon" />
            <div class="title">个人资料</div>
          </div>
          <div class="item" @click="hanldOpen('/#/control/wallet')">
            <img src="/image/wallet.png" class="icon" />
            <div class="title">我的钱包</div>
          </div>
          <div class="item" @click="hanldOpen('/#/control/apps')">
            <img src="/image/application.png" class="icon" />
            <div class="title">我的应用</div>
          </div>
          <div class="item" @click="hanldOpen('/#/control/orders')">
            <img src="/image/application.png" class="icon" />
            <div class="title">我的订单</div>
          </div>
          <div class="item" @click="hanldOpen('/#/control/site')">
            <img src="/image/user-site.png" class="icon" />
            <div class="title">我的站点</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      isLogin: false,
      user: {
        username: "",
        headimg: "",
      },
      page:1,
      datalist: [],
    };
  },
  created() {
    this.ob = new IntersectionObserver(e=>{
      if(e[0].isIntersecting){
        this.getBill();
      }
    });
    this.$nextTick(()=>{
      this.ob.observe(this.$refs.bottomRef);
    })
    this.init();
  },
  methods: {
    hanldOpen(path){
        const url = `http://www.kfadmin.net${path}`;
        window.open(url);
    },
    getUser(){
      var _this = this;
      _this.$http
        .useGet("/admin/PluginCloud/index")
        .then((e) => {
          const { data } = e;
          _this.user = data;
          _this.isLogin = true;
        })
        .catch((err) => {
          if ([11000,666,600].includes(err?.code)) {
            _this.$emit("update:openWin", "remote/cloud/login");
          }
        });
      },
    getBill(){
      var _this = this;
      _this.$http
        .useGet("/admin/PluginCloud/bill",{page:this.page})
        .then((e) => {
          const { data } = e;
          if(data.current_page>=data.last_page){
            this.ob.unobserve(this.$refs.bottomRef);
          }
          this.page++;
          const pageData=data.data;
          for (let index = 0; index < pageData.length; index++) {
            const element = pageData[index];
            this.datalist.push(element);
          }
          this.$nextTick(()=>{
            if(this.$refs.billListRef.scrollHeight<=this.$refs.billListRef.clientHeight){
              setTimeout(() => {
                this.getBill();
              }, 300);
            }
          })
        })
        .catch((err) => {
          if ([11000,666,600].includes(err?.code)) {
            _this.$emit("update:openWin", "remote/cloud/login");
          }
        });
    },
    // 初始化业务
    init() {
      this.getUser();
    },
  },
};
</script>

<style lang="scss" scoped>
.cloud-container {
  display: flex;
  height: 100%;
  background: #f5f5f5;
  .bill-container {
    flex: 1;
    overflow: hidden;
    .bill-title {
      font-size: 16px;
      font-weight: 700;
      height: 40px;
      display: flex;
      align-items: center;
      padding: 0 10px;
    }
    .bill-list {
      display: flex;
      flex-direction: column;
      gap: 20px;
      height: calc(100% - 40px);
      overflow-y: auto;
      padding: 0 10px;
      .item {
        background: #fff;
        box-shadow: 2px 3px 3px #e5e5e5;
        .order_no {
            border-bottom: 1px solid #e5e5e5;
            padding: 10px;
            font-size: 14px;
            color:#888;
        }
        .info-container{
            display: flex;
            padding: 10px;
            .bill-icon{
                width: 45px;
                height: 45px;
            }
            .info{
                flex: 1;
                padding-left: 10px;
                .title{
                    font-size: 16px;
                    font-weight: 700;
                }
                .date{
                    color:#888;
                    font-size: 14px;
                }
            }
            .price{
                width: 150px;
                display: flex;
                justify-content: center;
                align-items: center;
                font-size: 18px;
            }
            .inc{
                color:#18a058;
            }
            .dec{
                color:red;
            }
        }
      }
    }
  }
  .user-container {
    width: 230px;
    padding: 20px;
    .user-block {
      border: 1px solid #e5e5e5;
      border-radius: 10px;
      box-shadow: 3px 3px 3px #f1f1f1;
      padding: 20px;
      background: #fff;
      .user-title {
        font-size: 15px;
        font-weight: 700;
      }
      .user {
        display: flex;
        flex-direction: column;
        justify-content: center;
        text-align: center;
        line-height: 25px;
        .headimg {
          width: 100px;
          height: 100px;
          margin: 0 auto 10px auto;
          border-radius: 50%;
        }
        .nickname{
            font-size:18px;
        }
        .username {
            font-size: 14px;
        }
        .last-date {
          font-size: 14px;
          color: #888;
        }
      }
      .tools {
        display: flex;
        flex-direction: column;
        margin-top: 10px;
        gap: 10px;
        .item {
          display: flex;
          background: #fff;
          padding: 10px 20px;
          box-shadow: 2px 2px 3px #e5e5e5;
          border-radius: 3px;
          user-select: none;
          cursor: pointer;
          .icon {
            width: 35px;
            height: 35px;
          }
          .title {
            flex: 1;
            font-size: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
          }
        }
        .item:hover {
          background: #f8f8f8;
        }
      }
    }
  }
}
</style>
