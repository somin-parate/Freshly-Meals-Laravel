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
                                    href="#online"
                                    data-toggle="tab"
                                    >Online</a
                                >
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    href="#bank"
                                    data-toggle="tab"
                                    >Bank</a
                                >
                            </li>
                        </ul>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="active tab-pane" id="online">
                                <div class="row">
                                    <!-- <div class="card"> -->
                                    <div class="card-body table-responsive p-0">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Username</th>
                                                    <th>Plan Name</th>
                                                    <th>Transaction Id</th>
                                                    <th>Price</th>
                                                    <th>Plan Date</th>
                                                    <th>Is Pending?</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="online in onlinePayment.data"
                                                    :key="online.id"
                                                >
                                                    <td
                                                        class="text-capitalizetext-capitalize"
                                                        width="250px"
                                                    >
                                                        {{ online.user_name }}
                                                    </td>
                                                    <td
                                                        class="text-capitalizetext-capitalize"
                                                        width="250px"
                                                    >
                                                        {{ online.plan_name }}
                                                    </td>
                                                    <td
                                                        class="text-capitalizetext-capitalize"
                                                        width="250px"
                                                    >
                                                        {{
                                                            online.transaction_reference
                                                        }}
                                                    </td>
                                                    <td
                                                        class="text-capitalizetext-capitalize"
                                                        width="250px"
                                                    >
                                                        {{ online.price }}
                                                    </td>
                                                    <td
                                                        class="text-capitalizetext-capitalize"
                                                        width="250px"
                                                    >
                                                        {{ online.date }}
                                                    </td>
                                                    <td
                                                        class="text-capitalizetext-capitalize"
                                                        width="250px"
                                                    >
                                                        {{ online.pending }}
                                                    </td>
                                                    <td>
                                                        <button
                                                            v-if="
                                                                online.pending ==
                                                                    'Yes'
                                                            "
                                                            type="button"
                                                            class="btn btn-success"
                                                            @click="
                                                                paymentSuccessfullOnline(
                                                                    online.id
                                                                )
                                                            "
                                                        >
                                                            Confirm
                                                        </button>
                                                        <button
                                                            v-else
                                                            type="button"
                                                            class="btn btn-danger"
                                                        >
                                                            Success
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-footer">
                                        <div class="card-footer">
                                            <pagination
                                                :data="onlinePayment"
                                                @pagination-change-page="
                                                    getOnlineResults
                                                "
                                            ></pagination>
                                        </div>
                                    </div>
                                    <!-- </div> -->
                                </div>
                            </div>
                            <div class="tab-pane" id="bank">
                                <div class="row">
                                    <!-- <div class="card"> -->
                                    <div class="card-body table-responsive p-0">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Username</th>
                                                    <th>Plan Name</th>
                                                    <th>Transaction Id</th>
                                                    <th>Price</th>
                                                    <th>Plan Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="bank in bankRequests.data"
                                                    :key="bank.id"
                                                >
                                                    <td
                                                        class="text-capitalizetext-capitalize"
                                                        width="250px"
                                                    >
                                                        {{ bank.user_name }}
                                                    </td>
                                                    <td
                                                        class="text-capitalizetext-capitalize"
                                                        width="250px"
                                                    >
                                                        {{ bank.plan_name }}
                                                    </td>
                                                    <td
                                                        class="text-capitalizetext-capitalize"
                                                        width="250px"
                                                    >
                                                        {{ bank.order_number }}
                                                    </td>
                                                    <td
                                                        class="text-capitalizetext-capitalize"
                                                        width="250px"
                                                    >
                                                        {{ bank.price }}
                                                    </td>
                                                    <td
                                                        class="text-capitalizetext-capitalize"
                                                        width="250px"
                                                    >
                                                        {{ bank.date }}
                                                    </td>
                                                    <td>
                                                        <button
                                                            type="button"
                                                            class="btn btn-success"
                                                            v-if="
                                                                bank.status == 0
                                                            "
                                                            @click="
                                                                paymentSuccessfull(
                                                                    bank.id
                                                                )
                                                            "
                                                        >
                                                            Pay
                                                        </button>
                                                        <button
                                                            v-else
                                                            type="button"
                                                            class="btn btn-danger"
                                                        >
                                                            Paid
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-footer">
                                        <pagination
                                            :data="bankRequests"
                                            @pagination-change-page="
                                                getBankResults
                                            "
                                        ></pagination>
                                    </div>
                                    <!-- </div> -->
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
export default {
    data() {
        return {
            editmode: false,
            bankRequests: {},
            onlinePayment: {}
        };
    },
    methods: {
        getBankResults(page = 1) {
            this.$Progress.start();

            axios
                .get("/api/bankRequests?page=" + page)
                .then(({ data }) => (this.bankRequests = data.data));

            this.$Progress.finish();
        },

        getOnlineResults(page = 1) {
            this.$Progress.start();
            axios
                .get("/api/onlinePayments?page=" + page)
                .then(({ data }) => (this.onlinePayment = data.data));

            this.$Progress.finish();
        },

        getCatImage(img) {
            return `/images/api_images/${img}`;
        },

        loadBankRequests() {
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/bankRequests")
                    .then(({ data }) => (this.bankRequests = data.data));
            }
        },

        loadOnlineHistory() {
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/onlinePayments")
                    .then(({ data }) => (this.onlinePayment = data.data));
            }
        },

        paymentSuccessfull(id) {
            console.log(id);
            if (this.$gate.isAdmin()) {
                axios.get("/api/bankPayment/" + id).then(
                    response => {
                        console.log(response);
                        Swal.fire(
                            "Payment done!",
                            "Payment Successfull",
                            "success"
                        );
                        this.loadBankRequests();
                        //alert('response = ' + response)
                    },
                    error => {
                        console.log(error);
                        //alert('error = ' + error)
                    }
                );
            }
        },
        paymentSuccessfullOnline(id) {
            console.log(id);
            if (this.$gate.isAdmin()) {
                axios.get("/api/onlinePayment/" + id).then(
                    response => {
                        console.log(response);
                        Swal.fire(
                            "Payment done!",
                            "Payment Successfull",
                            "success"
                        );
                        window.location.reload();
                        this.loadBankRequests();
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
    mounted() {
        console.log("Component mounted.");
    },
    created() {
        this.$Progress.start();
        this.loadBankRequests();
        this.loadOnlineHistory();
        this.$Progress.finish();
    }
};
</script>
