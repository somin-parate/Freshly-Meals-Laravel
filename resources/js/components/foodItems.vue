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
                                    placeholder="Search Food Items..."
                                    id="search-input"
                                    v-model="keywords"
                                    class="form-control"
                                />
                            </div>
                        </div>
                        <div class="card-header">
                            <h3 class="card-title">Food Items List</h3>

                            <div class="card-tools">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-primary"
                                    @click="newModal"
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
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="offer in offers.data"
                                        :key="offer.id"
                                    >
                                        <td>{{ offer.id }}</td>
                                        <td class="text-capitalize">
                                            {{ offer.title }}
                                        </td>
                                        <td>
                                            <a
                                                href="#"
                                                @click="editModel(offer)"
                                            >
                                                <i class="fa fa-edit blue"></i>
                                            </a>
                                            <!-- <a
                                                href="#"
                                                @click="deleteItem(offer.id)"
                                            >
                                                <i class="fa fa-trash red"></i>
                                            </a> -->
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <pagination
                                :data="offers"
                                :limit="20"
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

            <!-- Add Modal -->
            <div
                class="modal fade"
                id="addNew"
                tabindex="-1"
                role="dialog"
                aria-labelledby="addNew"
                aria-hidden="true"
            >
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Create New Item</h5>
                            <button
                                type="button"
                                class="close"
                                data-dismiss="modal"
                                aria-label="Close"
                            >
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form @submit.prevent="createItem">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Item Name</label>
                                    <input
                                        v-model="form.title"
                                        type="text"
                                        name="title"
                                        class="form-control"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'title'
                                            )
                                        }"
                                        placeholder="Enter Food Item Name"
                                    />
                                    <has-error
                                        :form="form"
                                        field="title"
                                    ></has-error>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    data-dismiss="modal"
                                >
                                    Close
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    Create
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Add Modal -->

            <!-- edit Modal -->
            <div
                class="modal fade"
                id="editNew"
                tabindex="-1"
                role="dialog"
                aria-labelledby="editNew"
                aria-hidden="true"
            >
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Update Item</h5>
                            <button
                                type="button"
                                class="close"
                                data-dismiss="modal"
                                aria-label="Close"
                            >
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form @submit.prevent="updateItem">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Item Name</label>
                                    <input
                                        v-model="eform.title"
                                        type="text"
                                        name="title"
                                        class="form-control"
                                        :class="{
                                            'is-invalid': eform.errors.has(
                                                'title'
                                            )
                                        }"
                                        placeholder="Enter Food Item Name"
                                    />
                                    <has-error
                                        :form="eform"
                                        field="title"
                                    ></has-error>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    data-dismiss="modal"
                                >
                                    Close
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- edit Modal -->
        </div>
    </section>
</template>

<script>
export default {
    data() {
        return {
            editmode: false,
            offers: {},
            allItems: {},
            keywords: null,
            form: new Form({
                id: ""
            }),
            eform: new Form({
                id: "",
                title: ""
            })
        };
    },
    methods: {
        getResults(page = 1) {
            this.$Progress.start();

            axios.get("/api/food_items?page=" + page).then(({ data }) => {
                this.offers = data.data.items;
            });

            this.$Progress.finish();
        },

        newModal() {
            this.editmode = false;
            this.form.reset();
            $("#addNew").modal("show");
        },

        editModel(offer) {
            this.editmode = true;
            this.eform.reset();
            this.eform.fill(offer);
            $("#editNew").modal("show");
        },

        loadItemData() {
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/food_items")
                    .then(
                        ({ data }) => (
                            (this.offers = data.data.items),
                            (this.allItems = data.data.allItems),
                            console.log(this.offers)
                        )
                    );
            }
        },

        fetch() {
            axios
                .post("/api/search-foods", {
                    keyword: this.keywords,
                    offers: this.allItems
                })
                .then(
                    ({ data }) => (
                        console.log(this.offers),
                        (this.offers = {}),
                        (this.offers = data.data)
                    )
                );
        },

        createItem() {
            this.$Progress.start();

            this.form
                .post("/api/food_items")
                .then(data => {
                    $("#addNew").modal("hide");

                    Toast.fire({
                        icon: "success",
                        title: data.data.message
                    });
                    this.$Progress.finish();
                    this.loadItemData();
                })
                .catch(() => {
                    Toast.fire({
                        icon: "error",
                        title: "Some error occured! Please try again"
                    });
                });
        },

        updateItem() {
            this.$Progress.start();
            this.eform
                .put("/api/food_items/" + this.eform.id)
                .then(response => {
                    // success
                    $("#editNew").modal("hide");
                    Toast.fire({
                        icon: "success",
                        title: response.data.message
                    });
                    this.$Progress.finish();
                    this.loadItemData();
                })
                .catch(() => {
                    this.$Progress.fail();
                });
        },

        deleteItem(id) {
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
                        .delete("/api/food_items/" + id)
                        .then(() => {
                            Swal.fire(
                                "Deleted!",
                                "Item has been deleted.",
                                "success"
                            );
                            this.loadItemData();
                        })
                        .catch(data => {
                            Swal.fire("Failed!", data.message, "warning");
                        });
                }
            });
        }
    },
    mounted() {
        console.log("Component mounted.");
    },
    watch: {
        keywords: function keywords(after, before) {
            this.fetch();
        }
    },
    created() {
        this.$Progress.start();
        this.loadItemData();
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
