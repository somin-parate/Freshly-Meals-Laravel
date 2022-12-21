
<template>
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-primary card-outline">
          <div class="card-header">
            <h3 class="card-title">Bag Refund</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body p-0">
            <div class="mailbox-read-info">
              <h5>{{ feedbacks.subject }}</h5>
              <h6>
                From: {{ feedbacks.email }}
                <span class="mailbox-read-time float-right">{{
                  feedbacks.date
                }}</span>
              </h6>
            </div>
            <!-- /.mailbox-read-info -->
            <!-- /.mailbox-controls -->
            <div class="mailbox-read-message">
              <p>Hello Freshly Meals,</p>

              <!-- <div class="vertical-center"> -->
              <p>
                <button
                  type="button"
                  class="btn btn-success"
                  v-if="feedbacks.status == 1"
                  @click="markPaidRefund(feedbacks.id)"
                >
                  Pay Refund
                </button>
                <button v-else type="button" class="btn btn-danger">
                  Paid Refund
                </button>
              </p>

              <!-- </div> -->

              <p>Thank You.</p>
            </div>
            <!-- /.mailbox-read-message -->
          </div>
          <!-- /.card-footer -->
          <div class="card-footer"></div>
          <!-- /.card-footer -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.card -->
    </div>
    <!-- /.col -->
    <!-- /.row -->
  </section>
</template>

<script>
import datetime from "vuejs-datetimepicker";

export default {
  components: {
    datetime,
  },
  data() {
    return {
      feedbacks: {},
    };
  },
  methods: {
    getFeedResults(page = 1) {
      this.$Progress.start();

      axios
        .get("/api/feedback?page=" + page)
        .then(({ data }) => (this.feedbacks = data.data));

      this.$Progress.finish();
    },

    loadBagRefund() {
      if (this.$gate.isAdmin()) {
        axios
          .get("/api/viewBagRefund/" + this.$route.params.id)
          .then(({ data }) => {
            this.feedbacks = data.data;
          });
      }
    },

    markPaidRefund(id) {
      console.log(id);
      if (this.$gate.isAdmin()) {
        axios.get("/api/markPaidRefund/" + id).then(
          (response) => {
            console.log(response);
            Swal.fire("Payment done!", "Payment Successfull", "success");
            this.loadBagRefund();
            //alert('response = ' + response)
          },
          (error) => {
            console.log(error);
            //alert('error = ' + error)
          }
        );
      }
    },
  },
  created() {
    this.$Progress.start();
    this.loadBagRefund();
    this.$Progress.finish();
  },
};
</script>
<style>
.container {
  height: 200px;
  position: relative;
  border: 3px solid green;
}

.vertical-center {
  margin: 0;
  position: absolute;
  top: 50%;
  -ms-transform: translateY(-50%);
  transform: translateY(-50%);
}
</style>
<style>
.container {
  height: 200px;
  position: relative;
  border: 3px solid green;
}

.vertical-center {
  margin: 0;
  position: absolute;
  top: 50%;
  -ms-transform: translateY(-50%);
  transform: translateY(-50%);
}
</style>