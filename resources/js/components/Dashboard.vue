<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-warning elevation-1"
                            ><i class="fas fa-users"></i
                        ></span>

                        <div class="info-box-content">
                            <span class="info-box-text"
                                ><strong>Total Freshly Users</strong></span
                            >
                            <span class="info-box-number">{{ users }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-info elevation-1"
                            ><i class="fa fa-utensils nav-icon black"></i
                        ></span>

                        <div class="info-box-content">
                            <span class="info-box-text"
                                ><strong>Total Meals</strong></span
                            >
                            <span class="info-box-number">
                                {{ meals }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-danger elevation-1"
                            ><i class="fas fa-stroopwafel"></i
                        ></span>

                        <div class="info-box-content">
                            <span class="info-box-text"
                                ><strong>Total Food Items</strong></span
                            >
                            <span class="info-box-number">{{ foodItems }}</span>
                        </div>
                    </div>
                </div>

                <div class="clearfix hidden-md-up"></div>

                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-success elevation-1"
                            ><i class="fas fa-rocket"></i
                        ></span>

                        <div class="info-box-content">
                            <span class="info-box-text"
                                ><strong>Total Subscriptions</strong></span
                            >
                            <span class="info-box-number">{{
                                subscriptions
                            }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    data() {
        return {
            users: "",
            meals: "",
            foodItems: "",
            subscriptions: ""
        };
    },
    methods: {
        loadDashboardData() {
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/dashboardData")
                    .then(
                        ({ data }) => (
                            (this.users = data.data.users),
                            (this.meals = data.data.meals),
                            (this.foodItems = data.data.foodItems),
                            (this.subscriptions = data.data.subscriptions),
                            console.log(this.users)
                        )
                    );
            }
        }
    },
    mounted() {
        console.log("Component mounted.");
    },
    created() {
        this.$Progress.start();
        this.loadDashboardData();
        this.$Progress.finish();
    }
};
</script>
