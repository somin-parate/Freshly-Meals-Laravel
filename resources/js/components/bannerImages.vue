<template>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Custom Tabs -->
                <div class="card">
                    <div class="card-header p-2">
                        <div style="float: right;">
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
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="active tab-pane" id="help">
                                <!-- <div class="card"> -->
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Banner Image ID</th>
                                                <th>Image</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="banner in banners.data"
                                                :key="banner.id"
                                            >
                                                <td
                                                    class="text-capitalizetext-capitalize"
                                                >
                                                    {{ banner.id }}
                                                </td>
                                                <td>
                                                    <img
                                                        v-bind:src="
                                                            getBannerImage(
                                                                banner.image
                                                            )
                                                        "
                                                    />
                                                </td>
                                                <td>
                                                    <a
                                                        href="#"
                                                        @click="
                                                            editModel(banner)
                                                        "
                                                    >
                                                        <i
                                                            class="fa fa-edit blue"
                                                        ></i>
                                                    </a>
                                                    <a
                                                        href="#"
                                                        @click="
                                                            deleteBanner(
                                                                banner.id
                                                            )
                                                        "
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
                                <div class="card-footer">
                                    <pagination
                                        :data="banners"
                                        @pagination-change-page="getResults"
                                    ></pagination>
                                </div>
                                <!-- </div> -->
                            </div>
                        </div>
                        <!-- /.tab-content -->
                    </div>
                    <div class="card-footer"><!----></div>
                    <!-- /.card-body -->
                </div>
                <!-- ./card -->
            </div>
            <!-- /.col -->
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
                        <h5 class="modal-title">Add Banner Image</h5>
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form @submit.prevent="createBanner">
                        <div class="modal-body">
                            <div class="form-group">
                                <div class="custom-file">
                                    <label>Upload Banner Image</label>
                                    <input
                                        id="exampleInputFile1"
                                        type="file"
                                        name="image"
                                        class="form-control"
                                        @change="uploadCatImage"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'image'
                                            )
                                        }"
                                    />
                                    <has-error
                                        :form="form"
                                        field="image"
                                    ></has-error>
                                </div>
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
        <!-- Edit Modal -->
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
                        <h5 class="modal-title">Update</h5>
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form @submit.prevent="updateBanner">
                        <div class="modal-body">
                            <div class="form-group">
                                <div class="custom-file">
                                    <label>Upload Banner Image</label>
                                    <img
                                        v-bind:src="eform.currentImage"
                                        height="140px"
                                        width="100%"
                                        style="margin-bottom: 15px"
                                    />
                                    <input
                                        id="exampleInputFile1"
                                        type="file"
                                        name="image"
                                        class="form-control"
                                        @change="updateCatImage"
                                        :class="{
                                            'is-invalid': eform.errors.has(
                                                'image'
                                            )
                                        }"
                                    />
                                    <has-error
                                        :form="eform"
                                        field="image"
                                    ></has-error>
                                </div>
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
        <!-- Edit Modal -->
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
            banners: {},
            form: new Form({
                id: "",
                image: ""
            }),
            eform: new Form({
                id: "",
                image: "",
                currentImage: ""
            })
        };
    },
    methods: {
        getBannerImage(img) {
            return `/images/api_images/${img}`;
        },

        uploadCatImage(e) {
            let file = e.target.files[0];
            let reader = new FileReader();
            reader.onloadend = file => {
                this.form.image = reader.result;
            };
            reader.readAsDataURL(file);
        },

        updateCatImage(e) {
            let file = e.target.files[0];
            let reader = new FileReader();
            reader.onloadend = file => {
                this.eform.image = reader.result;
            };
            reader.readAsDataURL(file);
        },

        getResults(page = 1) {
            this.$Progress.start();

            axios
                .get("/api/banners?page=" + page)
                .then(({ data }) => (this.banners = data.data));

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
            this.eform.currentImage = this.getBannerImage(offer.image);
            $("#editNew").modal("show");
        },

        loadBannerData() {
            if (this.$gate.isAdmin() || this.$gate.isFinanceUser()) {
                axios
                    .get("/api/banners")
                    .then(({ data }) => (this.banners = data.data));
            }
        },

        createBanner() {
            this.$Progress.start();

            this.form
                .post("/api/banners")
                .then(data => {
                    $("#addNew").modal("hide");

                    Toast.fire({
                        icon: "success",
                        title: data.data.message
                    });
                    this.$Progress.finish();
                    this.loadBannerData();
                })
                .catch(() => {
                    Toast.fire({
                        icon: "error",
                        title: "Some error occured! Please try again"
                    });
                });
        },

        updateBanner() {
            this.$Progress.start();
            this.eform
                .put("/api/banners/" + this.eform.id)
                .then(response => {
                    // success
                    $("#editNew").modal("hide");
                    Toast.fire({
                        icon: "success",
                        title: response.data.message
                    });
                    this.$Progress.finish();
                    this.loadBannerData();
                })
                .catch(() => {
                    this.$Progress.fail();
                });
        },

        deleteBanner(id) {
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
                        .delete("/api/banners/" + id)
                        .then(() => {
                            Swal.fire(
                                "Deleted!",
                                "Banner Image has been deleted.",
                                "success"
                            );
                            this.loadBannerData();
                        })
                        .catch(data => {
                            Swal.fire("Failed!", data.message, "warning");
                        });
                }
            });
        }
    },
    created() {
        this.$Progress.start();
        this.loadBannerData();
        this.$Progress.finish();
    }
};
</script>
