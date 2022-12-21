
<template>
  <section class="content">
    <div class="row">
      <div class="col-md-12">
         <div class="card card-primary card-outline">
            <div class="card-header">
              <h3 class="card-title">Read Feedback</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
              <div class="mailbox-read-info">
                <h5>{{feedbacks.subject}}</h5>
                <h6>From: {{feedbacks.email}}
                  <span class="mailbox-read-time float-right">{{feedbacks.date}}</span></h6>
              </div>
              <!-- /.mailbox-read-info -->
              <!-- /.mailbox-controls -->
              <div class="mailbox-read-message">
                <p>Hello Freshly Meals,</p>

                <p>{{feedbacks.message}}</p>

                <p>Thank You.</p>

              </div>
              <!-- /.mailbox-read-message -->
            </div>
            <!-- /.card-footer -->
            <div class="card-footer">
            </div>
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

    loadFeedback() {
      if (this.$gate.isAdmin()) {
        axios
          .get("/api/viewFeedback/" + this.$route.params.id)
          .then(({ data }) => {
            this.feedbacks = data.data;
          });
      }
    },
  },
  created() {
    this.$Progress.start();
    this.loadFeedback();
    this.$Progress.finish();
  },
};
</script>