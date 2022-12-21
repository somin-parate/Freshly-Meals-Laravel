<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div
                        class="card"
                        v-if="$gate.isAdmin() || $gate.isFinanceUser()"
                    >
                        <div class="card-header">
                            <h3 class="card-title">Offer List</h3>

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
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Coupon Type</th>
                                        <th>Amount</th>
                                        <th>Coupon Code</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="offer in offers.data"
                                        :key="offer.id"
                                    >
                                        <td>{{ offer.id }}</td>
                                        <td>
                                            <img
                                                v-bind:src="
                                                    getOfferImage(offer.image)
                                                "
                                            />
                                        </td>
                                        <td class="text-capitalize">
                                            {{ offer.title }}
                                        </td>
                                        <td class="text-capitalize">
                                            {{ offer.start_date }}
                                        </td>
                                        <td class="text-capitalize">
                                            {{ offer.end_date }}
                                        </td>
                                        <td class="text-capitalize">
                                            {{
                                                offer.coupon_type === "1"
                                                    ? "Percentage(%)"
                                                    : "Integer"
                                            }}
                                        </td>
                                        <td class="text-capitalize">
                                            {{ offer.amount }}
                                        </td>
                                        <td class="text-capitalize">
                                            {{ offer.coupon_code }}
                                        </td>
                                        <td>
                                            <a
                                                href="#"
                                                @click="editModel(offer)"
                                            >
                                                <i class="fa fa-edit blue"></i>
                                            </a>
                                            <a
                                                href="#"
                                                @click="deleteOffer(offer.id)"
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
                                :data="offers"
                                @pagination-change-page="getResults"
                            ></pagination>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>

            <div v-if="!$gate.isAdmin() && !$gate.isFinanceUser()">
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
                            <h5 class="modal-title">Create New Offer</h5>
                            <button
                                type="button"
                                class="close"
                                data-dismiss="modal"
                                aria-label="Close"
                            >
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form @submit.prevent="createOffer">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Offer Name</label>
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
                                        placeholder="Enter Offer Name"
                                    />
                                    <has-error
                                        :form="form"
                                        field="title"
                                    ></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Existing Customer Email</label>
                                    <input
                                        v-model="form.customer_email"
                                        type="text"
                                        name="customer_email"
                                        class="form-control"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'customer_email'
                                            )
                                        }"
                                        placeholder="Enter Existing Customer Email"
                                    />
                                    <has-error
                                        :form="form"
                                        field="customer_email"
                                    ></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Coupon Code</label>
                                    <input
                                        v-model="form.coupon_code"
                                        type="text"
                                        name="coupon_code"
                                        class="form-control"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'coupon_code'
                                            )
                                        }"
                                        placeholder="Enter Coupon Code"
                                    />
                                    <has-error
                                        :form="form"
                                        field="coupon_code"
                                    ></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Coupon Amount</label>
                                    <input
                                        v-model="form.amount"
                                        type="text"
                                        name="amount"
                                        class="form-control"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'amount'
                                            )
                                        }"
                                        placeholder="Enter Coupon Amount"
                                    />
                                    <has-error
                                        :form="form"
                                        field="amount"
                                    ></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Coupon Type</label>
                                    <select
                                        name="coupon_type"
                                        class="form-control"
                                        v-model="form.coupon_type"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'coupon_type'
                                            )
                                        }"
                                    >
                                        <option value="">Select Type</option>
                                        <option value="1">Percentage(%)</option>
                                        <option value="2">Integer</option>
                                    </select>
                                    <has-error
                                        :form="form"
                                        field="coupon_type"
                                    ></has-error>
                                </div>
                                <div class="form-group">
                                    <div class="custom-file">
                                        <label>Upload Image</label>
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

                                <div class="form-group">
                                    <label
                                        >Start Date: ( Format Should Be:
                                        21-08-2021 )</label
                                    >
                                    <input
                                        v-model="form.start_date"
                                        type="text"
                                        name="start_date"
                                        class="form-control"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'start_date'
                                            )
                                        }"
                                        placeholder="21-08-2021"
                                    />
                                    <has-error
                                        :form="form"
                                        field="start_date"
                                    ></has-error>
                                </div>

                                <div class="form-group">
                                    <label
                                        >End Date: ( Format Should Be:
                                        21-08-2021 )</label
                                    >
                                    <input
                                        v-model="form.end_date"
                                        type="text"
                                        name="end_date"
                                        class="form-control"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'end_date'
                                            )
                                        }"
                                        placeholder="21-08-2021"
                                    />
                                    <has-error
                                        :form="form"
                                        field="end_date"
                                    ></has-error>
                                </div>

                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea
                                        v-model="form.description"
                                        name="description"
                                        class="form-control"
                                        rows="5"
                                        placeholder="Enter Offer Description"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'description'
                                            )
                                        }"
                                    ></textarea>
                                    <has-error
                                        :form="form"
                                        field="description"
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
                            <h5 class="modal-title">Update Offer</h5>
                            <button
                                type="button"
                                class="close"
                                data-dismiss="modal"
                                aria-label="Close"
                            >
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form @submit.prevent="updateOffer">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Offer Name</label>
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
                                        placeholder="Enter Offer Name"
                                    />
                                    <has-error
                                        :form="eform"
                                        field="title"
                                    ></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Existing Customer Email</label>
                                    <input
                                        v-model="eform.customer_email"
                                        type="text"
                                        name="customer_email"
                                        class="form-control"
                                        :class="{
                                            'is-invalid': eform.errors.has(
                                                'customer_email'
                                            )
                                        }"
                                        placeholder="Enter Existing Customer Email"
                                    />
                                    <has-error
                                        :form="eform"
                                        field="customer_email"
                                    ></has-error>
                                </div>

                                <div class="form-group">
                                    <label>Coupon Code</label>
                                    <input
                                        v-model="eform.coupon_code"
                                        type="text"
                                        name="coupon_code"
                                        class="form-control"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'coupon_code'
                                            )
                                        }"
                                        placeholder="Enter Coupon Code"
                                    />
                                    <has-error
                                        :form="eform"
                                        field="coupon_code"
                                    ></has-error>
                                </div>

                                <div class="form-group">
                                    <label>Coupon Amount</label>
                                    <input
                                        v-model="eform.amount"
                                        type="text"
                                        name="amount"
                                        class="form-control"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'amount'
                                            )
                                        }"
                                        placeholder="Enter Coupon Amount"
                                    />
                                    <has-error
                                        :form="eform"
                                        field="amount"
                                    ></has-error>
                                </div>

                                <div class="form-group">
                                    <label>Coupon Type</label>
                                    <select
                                        name="coupon_type"
                                        class="form-control"
                                        v-model="eform.coupon_type"
                                        :class="{
                                            'is-invalid': eform.errors.has(
                                                'coupon_type'
                                            )
                                        }"
                                    >
                                        <option value="">Select Type</option>
                                        <option value="1">Percentage(%)</option>
                                        <option value="2">Integer</option>
                                    </select>
                                    <has-error
                                        :form="eform"
                                        field="coupon_type"
                                    ></has-error>
                                </div>
                                <div class="form-group">
                                    <div class="custom-file">
                                        <label>Upload Image</label>
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

                                <div class="form-group">
                                    <label
                                        >Start Date: ( Format Should Be:
                                        21-08-2021 )</label
                                    >
                                    <input
                                        v-model="eform.start_date"
                                        type="text"
                                        name="start_date"
                                        class="eform-control"
                                        :class="{
                                            'is-invalid': eform.errors.has(
                                                'start_date'
                                            )
                                        }"
                                        placeholder="21-08-2021"
                                    />
                                    <has-error
                                        :form="eform"
                                        field="start_date"
                                    ></has-error>
                                </div>

                                <div class="form-group">
                                    <label
                                        >End Date: ( Format Should Be:
                                        21-08-2021 )</label
                                    >
                                    <input
                                        v-model="eform.end_date"
                                        type="text"
                                        name="end_date"
                                        class="eform-control"
                                        :class="{
                                            'is-invalid': eform.errors.has(
                                                'end_date'
                                            )
                                        }"
                                        placeholder="21-08-2021"
                                    />
                                    <has-error
                                        :form="eform"
                                        field="end_date"
                                    ></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea
                                        v-model="eform.description"
                                        name="description"
                                        class="form-control"
                                        rows="5"
                                        placeholder="Enter Offer Description"
                                        :class="{
                                            'is-invalid': eform.errors.has(
                                                'description'
                                            )
                                        }"
                                    ></textarea>
                                    <has-error
                                        :form="eform"
                                        field="description"
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
            allOffers: {},
            offers: {},
            form: new Form({
                id: "",
                title: "",
                description: "",
                image: "",
                coupon_code: "",
                start_date: "",
                end_date: "",
                coupon_type: "",
                customer_email: "",
                amount: ""
            }),
            eform: new Form({
                id: "",
                title: "",
                description: "",
                image: "",
                coupon_code: "",
                start_date: "",
                end_date: "",
                coupon_type: "",
                customer_email: "",
                amount: "",
                currentImage: ""
            })
        };
    },
    methods: {
        getOfferImage(img) {
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
                .get("/api/offers?page=" + page)
                .then(({ data }) => (this.offers = data.data));

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
            this.eform.currentImage = this.getOfferImage(offer.image);
            $("#editNew").modal("show");
        },

        loadOfferData() {
            if (this.$gate.isAdmin() || this.$gate.isFinanceUser()) {
                axios
                    .get("/api/offers")
                    .then(({ data }) => (this.offers = data.data));
            }
        },

        createOffer() {
            this.$Progress.start();

            this.form
                .post("/api/offers")
                .then(data => {
                    $("#addNew").modal("hide");

                    Toast.fire({
                        icon: "success",
                        title: data.data.message
                    });
                    this.$Progress.finish();
                    this.loadOfferData();
                })
                .catch(() => {
                    Toast.fire({
                        icon: "error",
                        title: "Some error occured! Please try again"
                    });
                });
        },

        updateOffer() {
            this.$Progress.start();
            this.eform
                .put("/api/offers/" + this.eform.id)
                .then(response => {
                    // success
                    $("#editNew").modal("hide");
                    Toast.fire({
                        icon: "success",
                        title: response.data.message
                    });
                    this.$Progress.finish();
                    this.loadOfferData();
                })
                .catch(() => {
                    this.$Progress.fail();
                });
        },

        deleteOffer(id) {
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
                        .delete("/api/offers/" + id)
                        .then(() => {
                            Swal.fire(
                                "Deleted!",
                                "Offer has been deleted.",
                                "success"
                            );
                            this.loadOfferData();
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
    created() {
        this.$Progress.start();
        this.loadOfferData();
        this.$Progress.finish();
    }
};
</script>
