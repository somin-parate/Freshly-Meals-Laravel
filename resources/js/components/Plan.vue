<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card" v-if="$gate.isAdmin()">
                        <div id="sort-bar">
                            <div class="form-group">
                                <input
                                    type="text"
                                    placeholder="Search Plans..."
                                    id="search-input"
                                    v-model="keywords"
                                    class="form-control"
                                />
                            </div>
                        </div>
                        <div class="card-header">
                            <h3 class="card-title">Meal Plan List</h3>

                            <div class="card-tools">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-primary"
                                    @click="$router.push('plans/create')"
                                >
                                    <i class="fa fa-plus-square"></i>
                                    Add New
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Image</th>
                                        <th>Description</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="plan in plans.data"
                                        :key="plan.id"
                                    >
                                        <td>{{ plan.id }}</td>
                                        <td
                                            class="text-capitalize"
                                            width="150px"
                                        >
                                            {{ plan.title }}
                                        </td>
                                        <td>
                                            <img
                                                v-bind:src="
                                                    getPlanTypeImage(plan.image)
                                                "
                                                width="120px"
                                                height="120px"
                                            />
                                        </td>
                                        <td width="40%">
                                            {{ plan.short_description }}
                                        </td>
                                        <td>
                                            <router-link
                                                v-bind:to="
                                                    '/plan/' + plan.id + '/edit'
                                                "
                                                ><i class="fa fa-edit blue"></i
                                            ></router-link>
                                            /
                                            <a
                                                href="#"
                                                @click="deletePlanType(plan.id)"
                                            >
                                                <i class="fa fa-trash red"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <pagination
                                :data="plans"
                                @pagination-change-page="getResults"
                            ></pagination>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>

            <div v-if="!$gate.isAdmin()">
                <not-found></not-found>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    data() {
        return {
            editmode: false,
            plans: {},
            allPlans: {},
            keywords: null,
            form: new Form({
                title: "",
                image: "",
                short_description: "",
                long_description: ""
            })
        };
    },
    methods: {
        getPlanTypeImage(img) {
            return `/images/api_images/${img}`;
        },
        getResults(page = 1) {
            this.$Progress.start();

            axios
                .get("/api/plan?page=" + page)
                .then(({ data }) => (this.plans = data.data.plans));

            this.$Progress.finish();
        },

        deletePlanType(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then(result => {
                if (result.value) {
                    this.form
                        .delete("api/plan/" + id)
                        .then(() => {
                            Swal.fire(
                                "Deleted!",
                                "Plan Type has been deleted.",
                                "success"
                            );
                            this.loadMealPlanTypes();
                        })
                        .catch(data => {
                            Swal.fire("Failed!", data.message, "warning");
                        });
                }
            });
        },

        loadMealPlanTypes() {
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/plan")
                    .then(
                        ({ data }) => (
                            (this.plans = data.data.plans),
                            (this.allPlans = data.data.allPlans)
                        )
                    );
            }
        },

        fetch() {
            axios
                .post("/api/search-plantypes", {
                    keyword: this.keywords,
                    plans: this.allPlans
                })
                .then(
                    ({ data }) => (
                        console.log(this.plans),
                        (this.plans = {}),
                        (this.plans = data.data)
                    )
                );
        }
    },
    mounted() {
        console.log("Plan Component mounted.");
    },
    watch: {
        keywords: function keywords(after, before) {
            this.fetch();
        }
    },
    created() {
        this.$Progress.start();
        this.loadMealPlanTypes();
        this.$Progress.finish();
    }
};
</script>
<style>
#sort-bar {
    width: 80%;
    display: flex;
    flex-wrap: wrap;
    padding: 10px;
}

#search-input {
    margin-right: 10px;
}
</style>
