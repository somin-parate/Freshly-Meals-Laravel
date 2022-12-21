<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-24">
                    <div class="card-tools">
                        <div id="printMe">
                            <a
                                :href="url"
                                type="button"
                                class="btn btn-sm btn-success"
                                style="padding: 5px 5px 5px 5px; margin-bottom: 10px;"
                                @click="print"
                            >
                                <i class="fa fa-print" aria-hidden="true"></i>
                                Export Excel
                            </a>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Meal Preparation</h3>
                            <h3
                                class="card-title"
                                style="text-align : center;padding-left: 340px;"
                            >
                                <strong>{{ date }}</strong>
                            </h3>
                        </div>
                        <div class="card-body">
                            <table
                                id="example1"
                                class="table table-bordered table-striped"
                            >
                                <thead>
                                    <tr style="text-align: center">
                                        <th rowspan="2">Food Items</th>
                                        <th
                                            colspan="2"
                                            v-for="shortcode in shortcodes"
                                            :key="shortcode.id"
                                        >
                                            {{ shortcode.shortcode }}
                                        </th>
                                        <th rowspan="2">Grand Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th></th>
                                        <th
                                            v-for="index in shortcodes_length"
                                            :key="index"
                                            style="text-align: center"
                                        >
                                            <span v-if="index % 2 !== 0"
                                                >Male</span
                                            >
                                            <span v-else>Female</span>
                                        </th>
                                        <th></th>
                                    </tr>
                                    <tr v-for="(item, name) in items">
                                        <td style="text-align: center">
                                            {{ item.item_name }}
                                        </td>
                                        <td
                                            style="text-align: center"
                                            v-for="(value,
                                            propertyName,
                                            index) in item.plan_data"
                                        >
                                            {{ value.item_count }}
                                            <!-- <div v-if="value.item_count > 0">
                                                <button
                                                    v-if="
                                                        value.item_status ===
                                                            'Ready'
                                                    "
                                                    type="button"
                                                    class="btn btn-success"
                                                >
                                                    <i
                                                        class="fas fa-check-circle"
                                                        aria-hidden="true"
                                                    ></i>
                                                    Done
                                                </button>
                                                <button
                                                    v-else
                                                    type="button"
                                                    class="btn btn-secondary"
                                                    @click="
                                                        doneMeal(
                                                            $event,
                                                            item.id,
                                                            propertyName
                                                        )
                                                    "
                                                >
                                                    <i
                                                        class="fas fa-circle"
                                                        aria-hidden="true"
                                                    ></i>
                                                    Mark
                                                </button>
                                            </div> -->
                                        </td>
                                        <td style="text-align: center">
                                            <strong>{{
                                                item.total_count
                                            }}</strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
</template>

<script>
export default {
    data() {
        return {
            shortcodes: {},
            shortcodes_length: 0,
            items: {},
            completed: {},
            date: {},
            url: ""
        };
    },
    computed: {
        totalCount(total) {
            console.log(total);
            // let sum = 0;
            // total.forEach(item => (sum += item.total_count));
            // return sum;
        }
    },
    methods: {
        loadShortcodes() {
            if (this.$gate.isKithcenUser() || this.$gate.isAdmin()) {
                axios.get("/api/planShortcodes").then(({ data }) => {
                    this.shortcodes = data.data;
                    this.shortcodes_length = this.shortcodes.length * 2;
                });
            }
        },
        print() {
            if (this.$gate.isKithcenUser() || this.$gate.isAdmin()) {
                this.url = "/api/getPreparationListExportCsv";
            }
        },

        loadPreparationMeal() {
            if (this.$gate.isKithcenUser() || this.$gate.isAdmin()) {
                axios
                    .get("/api/getPreparationList")
                    .then(({ data }) => (this.items = data.data));
            }
        },

        setCompletedMealData(event, itemId, property) {
            var spilited = property.split("_");
            var shortcode = spilited[0];
            var gender = spilited[1];

            this.completed = {};
            this.completed.food_item_id = itemId;
            this.completed.plan_shortcode = shortcode;
            this.completed.gender = gender;
        },
        date_function: function() {
            var months = [
                "JANUARY",
                "FEBRUARY",
                "MARCH",
                "APRIL",
                "MAY",
                "JUNE",
                "JULY",
                "AUGUST",
                "SEPTEMBER",
                "OCTOBER",
                "NOVEMBER",
                "DECEMBER"
            ];
            var currentDate = new Date();
            var monthName = months[currentDate.getMonth()];
            console.log(monthName);
            var result = currentDate.setDate(currentDate.getDate() + 3);
            console.log(new Date(result).getDate());
            this.date =
                new Date(result).getDate() + " " + monthName + " BREAKDOWN";
        },

        doneMeal(event, itemId, property) {
            if (this.$gate.isKithcenUser() || this.$gate.isAdmin()) {
                Swal.fire({
                    title: "Are you sure?",
                    text: "You have done this item !",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, done it!"
                }).then(result => {
                    this.setCompletedMealData(event, itemId, property);
                    if (result.value) {
                        axios
                            .post("/api/markCompleted", this.completed)
                            .then(response => {
                                Swal.fire(
                                    "Completed!",
                                    "Item has done.",
                                    "success"
                                );
                                this.loadPreparationMeal();
                            })
                            .catch(error => {
                                console.log(error);
                            });
                    }
                });
            }
        }
    },
    created() {
        this.$Progress.start();
        this.loadShortcodes();
        this.loadPreparationMeal();
        this.$Progress.finish();
    },
    mounted() {
        this.date_function();
    }
};
</script>

<style></style>
