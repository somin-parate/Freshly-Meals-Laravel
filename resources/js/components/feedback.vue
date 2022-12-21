<template>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a
                                    class="nav-link active"
                                    href="#feedback"
                                    data-toggle="tab"
                                >
                                    <i class="fas fa-inbox"></i> Feedbacks</a
                                >
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    href="#bug"
                                    data-toggle="tab"
                                >
                                    <i class="far fa-envelope"></i> Bugs</a
                                >
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    href="#refund"
                                    data-toggle="tab"
                                >
                                    <i class="far fa-file-alt"></i> Bag
                                    Refunds</a
                                >
                            </li>
                        </ul>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="active tab-pane" id="feedback">
                                <div class="row">
                                    <div class="card-body p-0">
                                        <div
                                            class="table-responsive mailbox-messages"
                                        >
                                            <table class="table table-hover">
                                                <tbody>
                                                    <tr
                                                        v-for="feed in feedbacks.data"
                                                        :key="feed.id"
                                                    >
                                                        <!-- <td class="mailbox-name">
                              <a v-bind:href="'/feeds/' + feed.id + '/view'">
                                {{ feed.name }}</a
                              >
                            </td> -->
                                                        <td>
                                                            <router-link
                                                                v-bind:to="
                                                                    '/freshly_users/' +
                                                                        feed.user_id +
                                                                        '/view'
                                                                "
                                                                >{{
                                                                    feed.name
                                                                }}</router-link
                                                            >
                                                        </td>
                                                        <td id="overflowTest">
                                                            {{ feed.message }}
                                                        </td>
                                                        <td
                                                            class="mailbox-date"
                                                        >
                                                            {{ feed.date }}
                                                        </td>
                                                        <td>
                                                            <a
                                                                class="btn btn-success"
                                                                v-bind:href="
                                                                    '/feeds/' +
                                                                        feed.id +
                                                                        '/view'
                                                                "
                                                                >View Details</a
                                                            >
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="card-footer">
                                            <pagination
                                                :data="feedbacks"
                                                @pagination-change-page="
                                                    getFeedResults
                                                "
                                            ></pagination>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="bug">
                                <div class="row">
                                    <div class="card-body p-0">
                                        <div
                                            class="table-responsive mailbox-messages"
                                        >
                                            <table class="table table-hover">
                                                <tbody>
                                                    <tr
                                                        v-for="bug in bugs.data"
                                                        :key="bug.id"
                                                    >
                                                        <!-- <td
                                                            class="mailbox-name"
                                                        >
                                                            <a
                                                                v-bind:href="
                                                                    '/bugs/' +
                                                                        bug.id +
                                                                        '/view'
                                                                "
                                                            >
                                                                {{
                                                                    bug.name
                                                                }}</a
                                                            >
                                                        </td> -->
                                                        <td>
                                                            <router-link
                                                                v-bind:to="
                                                                    '/freshly_users/' +
                                                                        bug.user_id +
                                                                        '/view'
                                                                "
                                                                >{{
                                                                    bug.name
                                                                }}</router-link
                                                            >
                                                        </td>

                                                        <td
                                                            class="mailbox-subject"
                                                        >
                                                            {{ bug.message }}
                                                        </td>
                                                        <td
                                                            class="mailbox-date"
                                                        >
                                                            {{ bug.date }}
                                                        </td>
                                                        <td>
                                                            <a
                                                                class="btn btn-success"
                                                                v-bind:href="
                                                                    '/bugs/' +
                                                                        bug.id +
                                                                        '/view'
                                                                "
                                                                >View Details</a
                                                            >
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="card-footer">
                                            <pagination
                                                :data="bugs"
                                                @pagination-change-page="
                                                    getBugsResults
                                                "
                                            ></pagination>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="refund">
                                <div class="row">
                                    <div class="card-body p-0">
                                        <div
                                            class="table-responsive mailbox-messages"
                                        >
                                            <table class="table table-hover">
                                                <tbody>
                                                    <tr
                                                        v-for="refund in refunds.data"
                                                        :key="refund.id"
                                                    >
                                                        <!-- <td
                                                            class="mailbox-name"
                                                        >
                                                            <a
                                                                v-bind:href="
                                                                    '/refunds/' +
                                                                        refund.id +
                                                                        '/view'
                                                                "
                                                            >
                                                                {{
                                                                    refund.name
                                                                }}</a
                                                            >
                                                        </td> -->
                                                        <td>
                                                            <router-link
                                                                v-bind:to="
                                                                    '/freshly_users/' +
                                                                        refund.user_id +
                                                                        '/view'
                                                                "
                                                                >{{
                                                                    refund.name
                                                                }}</router-link
                                                            >
                                                        </td>
                                                        <td
                                                            class="mailbox-subject"
                                                        >
                                                            <a
                                                                v-bind:href="
                                                                    '/refunds/' +
                                                                        refund.id +
                                                                        '/view'
                                                                "
                                                            >
                                                                {{
                                                                    "Applied for refund"
                                                                }}</a
                                                            >
                                                        </td>
                                                        <td
                                                            class="mailbox-date"
                                                        >
                                                            {{ refund.date }}
                                                        </td>
                                                        <td>
                                                            <a
                                                                class="btn btn-success"
                                                                v-bind:href="
                                                                    '/refunds/' +
                                                                        refund.id +
                                                                        '/view'
                                                                "
                                                                >View Details</a
                                                            >
                                                        </td>
                                                        <!-- <button
                              type="button"
                              class="btn btn-success"
                              v-if="refund.status == 0"
                              @click="markPaidRefund(refund.id)"
                            >
                              Pay Refund
                            </button>
                            <button v-else type="button" class="btn btn-danger">
                              Paid Refund
                            </button> -->
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="card-footer">
                                            <pagination
                                                :data="refunds"
                                                @pagination-change-page="
                                                    getRefundResults
                                                "
                                            ></pagination>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div>
                    <!-- /.card-body -->
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
        datetime
    },
    data() {
        return {
            feedbacks: {},
            bugs: {},
            refunds: {},
            paid: {},
            selectedUser: null,
            phaseData: null, // no value
            showPhase: ""
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

        selectRow(id) {
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/bugs" + id)
                    .then(({ data }) => (this.bugs = data.data));
            }
        },

        getBugsResults(page = 1) {
            this.$Progress.start();

            axios
                .get("/api/bugs?page=" + page)
                .then(({ data }) => (this.bugs = data.data));

            this.$Progress.finish();
        },

        getRefundResults(page = 1) {
            this.$Progress.start();

            axios
                .get("/api/refunds?page=" + page)
                .then(({ data }) => (this.refunds = data.data));

            this.$Progress.finish();
        },

        getMealImage(img) {
            return `/images/api_images/${img}`;
        },

        clicked: function() {
            console.log(paid);
            this.paid = "clicked";
        },
        loadFeedbackList() {
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/feedback")
                    .then(({ data }) => (this.feedbacks = data.data));
            }
        },

        loadBugsList() {
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/bugs")
                    .then(({ data }) => (this.bugs = data.data));
            }
        },

        loadRefundsList() {
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/refunds")
                    .then(({ data }) => (this.refunds = data.data));
            }
        },

        markPaidRefund(id) {
            console.log(id);
            if (this.$gate.isAdmin()) {
                axios.get("/api/markPaidRefund/" + id).then(
                    response => {
                        console.log(response);
                        Swal.fire(
                            "Payment done!",
                            "Payment Successfull",
                            "success"
                        );
                        this.loadRefundsList();
                        //alert('response = ' + response)
                    },
                    error => {
                        console.log(error);
                        //alert('error = ' + error)
                    }
                );
            }
        }
    },
    created() {
        this.$Progress.start();
        this.loadFeedbackList();
        this.loadBugsList();
        this.loadRefundsList();
        this.$Progress.finish();
    }
};
</script>

<style>
#overflowTest {
    width: 600px;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
