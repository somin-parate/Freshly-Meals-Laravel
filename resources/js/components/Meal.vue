<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card" v-if="$gate.isAdmin()">
                        <form @submit.prevent="">
                            <div
                                id="sort-bar"
                                style="width: 100%;
                                display: flex;
                                flex-wrap: wrap;
                                padding: 10px;
                                justify-content: space-between;"
                            >
                                <div class="form-group" style="display: flex;">
                                    <input
                                        type="text"
                                        placeholder="Search Meals..."
                                        id="search-input"
                                        v-model="keywords"
                                        class="form-control"
                                    />
                                    <date-picker
                                        v-model="freshlyDate"
                                        id="freshlyDate"
                                        format="YYYY-MM-DD"
                                        type="date"
                                        placeholder="Select date"
                                        width="400px"
                                    ></date-picker>
                                    <select
                                        class="form-control"
                                        name="meal_week"
                                        id="plan_name"
                                        style="margin-left: 10px;"
                                    >
                                        <option value="">Select Plan</option>
                                        <option
                                            v-for="planName in allPlans"
                                            :key="planName.id"
                                            v-bind:value="planName.id"
                                            >{{ planName.title }}</option
                                        >
                                    </select>
                                    <button
                                        class="btn btn-primary"
                                        style="margin-left: 10px;"
                                        @click="findMeals()"
                                    >
                                        Filter
                                    </button>
                                </div>
                                <!-- <div style="float: right">
                                    <button
                                        class="btn btn-danger"
                                        style="float: right;"
                                        @click="backButton()"
                                    >
                                        Back
                                    </button>
                                </div> -->
                            </div>
                            <div class="card-header">
                                <h3 class="card-title">Meal List</h3>

                                <div class="card-tools">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-primary"
                                        @click="$router.push('meals/create')"
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
                                            <th>Plan Name</th>
                                            <th>Category</th>
                                            <th>Meal Date</th>
                                            <th>Meal/Snack</th>
                                            <th>Gender</th>
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
                                                width="250px"
                                            >
                                                {{ plan.name }}
                                            </td>
                                            <td>
                                                <img
                                                    v-bind:src="
                                                        getMealImage(plan.image)
                                                    "
                                                    width="120px"
                                                    height="120px"
                                                />
                                            </td>
                                            <td>{{ plan.plan_name }}</td>
                                            <td>{{ plan.meal_type }}</td>
                                            <td>{{ plan.meal_begin_at }}</td>
                                            <td>{{ plan.meal_info }}</td>
                                            <td>{{ plan.gender }}</td>
                                            <td>
                                                <!-- <router-link
                                                    v-bind:to="
                                                        '/meal/' +
                                                            plan.id +
                                                            '/edit'
                                                    "
                                                    ><i
                                                        class="fa fa-edit blue"
                                                    ></i
                                                ></router-link>
                                                / -->
                                                <a
                                                    href="#"
                                                    @click="deleteMeal(plan.id)"
                                                >
                                                    <i
                                                        class="fa fa-trash red"
                                                    ></i>
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
                                    :limit="20"
                                    @pagination-change-page="getResults"
                                ></pagination>
                            </div>
                        </form>
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
import datetime from "vuejs-datetimepicker";
import DatePicker from "vue2-datepicker";
import "vue2-datepicker/index.css";
export default {
    components: {
        datetime,
        DatePicker
    },
    data() {
        return {
            editmode: false,
            freshlyDate: "",
            plans: [],
            allMeals: [],
            allPlans: [],
            keywords: null,
            mealsByDate: {},
            form: new Form({
                meal_plan_type: "",
                meal_name: "",
                meal_category: "",
                meal_image: "",
                meal_macros: ""
            })
        };
    },
    methods: {
        getMealImage(img) {
            return `/images/api_images/${img}`;
        },
        getResults(page = 1) {
            this.$Progress.start();

            axios
                .get("/api/meal?page=" + page)
                .then(({ data }) => (this.plans = data.data.meals));

            this.$Progress.finish();
        },

        backButton() {
            history.back();
        },
        editModel(meals) {
            this.editmode = true;
            this.eform.reset();
            this.eform.fill(meals);
            this.eform.currentImage = this.getMealImage(meal.image);
            $("#editNew").modal("show");
        },

        deleteMeal(id) {
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
                        .delete("api/meal/" + id)
                        .then(() => {
                            Swal.fire(
                                "Deleted!",
                                "Meal has been deleted.",
                                "success"
                            );
                            this.loadMeals();
                        })
                        .catch(data => {
                            Swal.fire("Failed!", data.message, "warning");
                        });
                }
            });
        },

        renderMeal(plan) {
            this.plans = plan;
            console.log(this.plans);
        },

        findMeals() {
            const meal_date = {
                date: this.freshlyDate,
                plan_id: document.getElementById("plan_name").value
            };
            if (this.$gate.isAdmin()) {
                axios
                    .post("/api/MealListDateAdmin", meal_date)
                    .then(response => {
                        this.renderMeal(response.data.data[0]);
                    })
                    .catch(error => {
                        console.log(error);
                    });
            }
        },

        findMealsByPlan() {
            const meal_date = {
                date: datetime
            };
            if (this.$gate.isAdmin()) {
                axios
                    .post("/api/MealListDateAdmin", meal_date)
                    .then(response => {
                        this.renderMeal(response.data.data[0]);
                    })
                    .catch(error => {
                        console.log(error);
                    });
            }
        },

        getAllPlanTypes() {
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/getAllPlanTypes")
                    .then(
                        ({ data }) => (
                            (this.allPlans = data.data[0]),
                            console.log(this.allPlans)
                        )
                    );
            }
        },

        loadMeals() {
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/meal")
                    .then(
                        ({ data }) => (
                            (this.plans = data.data.meals),
                            (this.allMeals = data.data.allMeals),
                            console.log(this.allMeals)
                        )
                    );
            }
        },

        fetch() {
            axios
                .post("/api/search-meals", {
                    keyword: this.keywords,
                    plans: this.allMeals
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
        this.loadMeals();
        this.getAllPlanTypes();
        this.$Progress.finish();
    }
};
</script>

<style>
#sort-bar {
    width: 100%;
    display: flex;
    flex-wrap: wrap;
    padding: 10px;
    justify-content: space-between;
}
.mx-datepicker {
    width: 400px;
}

#search-input {
    margin-right: 10px;
}
</style>
