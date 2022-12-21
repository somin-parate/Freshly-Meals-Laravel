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
                            <h3 class="card-title">Delivery Preparation</h3>
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
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Google Address Code</th>
                                        <th>Emirate</th>
                                        <th>Delivery Time Slot</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, name) in meal_items">
                                        <td>{{ item.user_name }}</td>
                                        <td>{{ item.phone }}</td>
                                        <td>{{ item.address }}</td>
                                        <td>{{ item.google_code }}</td>
                                        <td>{{ item.emirate }}</td>
                                        <td>{{ item.time_slot }}</td>
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
            url: ""
        };
    },
    methods: {
        loadPreparationMeal() {
            if (this.$gate.isParcelUser() || this.$gate.isAdmin()) {
                axios
                    .get("/api/getDeliveryList")
                    .then(({ data }) => (this.meal_items = data.data));
            }
        },

        print() {
            if (this.$gate.isKithcenUser() || this.$gate.isAdmin()) {
                this.url = "/api/getDeliveryListExportCsv";
            }
        }
    },
    mounted() {
        // $(function () {
        //   $("#example1").DataTable({
        //     paging: true,
        //     lengthChange: false,
        //     searching: true,
        //     ordering: true,
        //     iDisplayLength: 5,
        //     // info: true,
        //     autoWidth: false,
        //     responsive: true,
        //   });
        // });
    },
    created() {
        this.$Progress.start();
        this.loadPreparationMeal();
        this.$Progress.finish();
    }
};
</script>

<style></style>
