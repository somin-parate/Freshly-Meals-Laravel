<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
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
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Parcel Preparation</h3>
                            <h3
                                class="card-title"
                                style="text-align : center;padding-left: 340px;"
                            >
                                <strong>{{ actual_date }}</strong>
                            </h3>
                        </div>
                        <div class="card-body">
                            <table
                                id="example1"
                                class="table table-bordered dataTable dtr-inline"
                                role="grid"
                                aria-describedby="example1_info"
                            >
                                <thead>
                                    <tr role="row">
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Gender</th>
                                        <th>Meal Plan</th>
                                        <th>Cutlery?</th>
                                        <th>Meal Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, name) in meal_items">
                                        <td>{{ item.user_name }}</td>
                                        <td>{{ item.email }}</td>
                                        <td>{{ item.gender }}</td>
                                        <td>{{ item.plan_name }}</td>
                                        <td>{{ item.cutlery }}</td>
                                        <td>
                                            <table
                                                width="100%"
                                                v-for="(value,
                                                propertyName,
                                                index) in item.meal_list"
                                            >
                                                <tr bgcolor="#48d7ad">
                                                    <td
                                                        colspan="3"
                                                        style="font-weight: bold; font-size: 20px"
                                                        align="center"
                                                    >
                                                        {{ propertyName }}
                                                    </td>
                                                </tr>
                                                <tr
                                                    v-for="(item1,
                                                    name1) in value"
                                                >
                                                    <td>{{ item1.item }}</td>
                                                    <td>{{ item1.qty }}</td>
                                                    <!-- <td>
                                                        <button
                                                            type="button"
                                                            class="btn btn-success"
                                                            v-if="
                                                                item1.status ===
                                                                    'Ready'
                                                            "
                                                        >
                                                            <i
                                                                class="fas fa-check-circle"
                                                                aria-hidden="true"
                                                            ></i>
                                                            Ready
                                                        </button>
                                                        <button
                                                            type="button"
                                                            class="btn btn-danger"
                                                            v-else
                                                        >
                                                            <i
                                                                class="fas fa-history"
                                                                aria-hidden="true"
                                                            ></i>
                                                            In Progress
                                                        </button>
                                                    </td> -->
                                                </tr>
                                            </table>
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
            meal_items: {},
            actual_date: {},
            current_date: {},
            url: ""
        };
    },
    methods: {
        loadPreparationMeal() {
            if (this.$gate.isParcelUser() || this.$gate.isAdmin()) {
                axios
                    .get("/api/getParcelsList")
                    .then(({ data }) => (this.meal_items = data.data));
            }
        },

        print() {
            if (this.$gate.isKithcenUser() || this.$gate.isAdmin()) {
                this.url = "/api/getParcelsListExportCsv";
            }
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
            var result1 = currentDate.setDate(currentDate.getDate());
            console.log(new Date(result).getDate());
            this.actual_date =
                new Date(result).getDate() + " " + monthName + " BREAKDOWN";
            this.current_date =
                new Date(result1).getDate() + " " + monthName + " BREAKDOWN";
        }
    },
    mounted() {
        this.date_function();
    },
    created() {
        this.$Progress.start();
        this.loadPreparationMeal();
        this.$Progress.finish();
    }
};
</script>

<style></style>
