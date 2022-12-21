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
                                                <th>Category</th>
                                                <th>Subject</th>
                                                <th>Message</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="faq in arrayFaqs"
                                                :key="faq.id"
                                            >
                                                <td
                                                    class="text-capitalizetext-capitalize"
                                                >
                                                    {{ faq.category_name }}
                                                </td>
                                                <td
                                                    class="text-capitalizetext-capitalize"
                                                >
                                                    {{ faq.subject }}
                                                </td>
                                                <td
                                                    class="text-capitalizetext-capitalize"
                                                >
                                                    {{ faq.message }}
                                                </td>
                                                <td>
                                                    <a
                                                        href="#"
                                                        @click="
                                                            editModelFaq(faq)
                                                        "
                                                    >
                                                        <i
                                                            class="fa fa-edit blue"
                                                        ></i>
                                                    </a>
                                                    <a
                                                        href="#"
                                                        @click="
                                                            deleteCategory(
                                                                faq.id
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
                                        :data="paginateFaqs"
                                        @pagination-change-page="getFaqsResults"
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
                        <h5 class="modal-title">Add</h5>
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form @submit.prevent="createCategory">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Category</label>
                                <select
                                    class="form-control"
                                    v-model="form.category"
                                    :class="{
                                        'is-invalid': form.errors.has(
                                            'category'
                                        )
                                    }"
                                >
                                    <option value="">
                                        Select Category
                                    </option>
                                    <option
                                        v-for="cate in categoriesTypes"
                                        :value="cate.value"
                                    >
                                        {{ cate.text }}
                                    </option>
                                </select>
                                <has-error
                                    :form="form"
                                    field="category"
                                ></has-error>
                            </div>

                            <div class="form-group">
                                <label>Subject</label>
                                <input
                                    type="text"
                                    name="subject"
                                    class="form-control"
                                    placeholder="Enter Subject"
                                    v-model="form.subject"
                                    :class="{
                                        'is-invalid': form.errors.has('subject')
                                    }"
                                />
                                <has-error
                                    :form="form"
                                    field="subject"
                                ></has-error>
                            </div>
                            <div class="form-group">
                                <label>Message</label>
                                <input
                                    type="text"
                                    name="message"
                                    class="form-control"
                                    placeholder="Enter Message"
                                    v-model="form.message"
                                    :class="{
                                        'is-invalid': form.errors.has('message')
                                    }"
                                />
                                <has-error
                                    :form="form"
                                    field="message"
                                ></has-error>
                            </div>
                            <div class="form-group">
                                <label>Type</label>
                                <select
                                    class="form-control"
                                    v-model="form.type"
                                    :class="{
                                        'is-invalid': form.errors.has('type')
                                    }"
                                >
                                    <option value="">Select Type</option>
                                    <option
                                        v-for="typ in chatTypes"
                                        :value="typ.value"
                                    >
                                        {{ typ.text }}
                                    </option>
                                </select>
                                <has-error
                                    :form="form"
                                    field="type"
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

                    <form @submit.prevent="updateCategory">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Category</label>
                                <select
                                    class="form-control"
                                    name="category"
                                    v-model="eform.category"
                                    :class="{
                                        'is-invalid': eform.errors.has(
                                            'category'
                                        )
                                    }"
                                >
                                    <option value="">
                                        Select Category
                                    </option>
                                    <option
                                        v-for="cate in categoriesTypes"
                                        :value="cate.value"
                                    >
                                        {{ cate.text }}
                                    </option>
                                </select>
                                <has-error
                                    :form="eform"
                                    field="category"
                                ></has-error>
                            </div>
                            <div class="form-group">
                                <label>Subject</label>
                                <input
                                    type="text"
                                    name="subject"
                                    class="form-control"
                                    placeholder="Enter Subject"
                                    v-model="eform.subject"
                                    :class="{
                                        'is-invalid': eform.errors.has(
                                            'subject'
                                        )
                                    }"
                                />
                                <has-error
                                    :form="eform"
                                    field="subject"
                                ></has-error>
                            </div>
                            <div class="form-group">
                                <label>Message</label>
                                <input
                                    type="text"
                                    name="message"
                                    class="form-control"
                                    placeholder="Enter Message"
                                    v-model="eform.message"
                                    :class="{
                                        'is-invalid': eform.errors.has(
                                            'message'
                                        )
                                    }"
                                />
                                <has-error
                                    :form="eform"
                                    field="message"
                                ></has-error>
                            </div>
                            <div class="form-group">
                                <label>Type</label>
                                <select
                                    class="form-control"
                                    name="type"
                                    v-model="eform.type"
                                    :class="{
                                        'is-invalid': eform.errors.has('type')
                                    }"
                                >
                                    <option value="">Select Type</option>
                                    <option
                                        v-for="typ in chatTypes"
                                        :value="typ.value"
                                    >
                                        {{ typ.text }}
                                    </option>
                                </select>
                                <has-error
                                    :form="eform"
                                    field="type"
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
            faqs: {},
            arrayFaqs: {},
            paginateFaqs: {},
            categoriesTypes: [
                { value: "subscriptions", text: "Subscriptions" },
                { value: "pricing", text: "Pricing, Payments & Refunds" },
                {
                    value: "food_allergies",
                    text: "Food Allergies & Preferences"
                },
                { value: "weight_manage", text: "Weight Management Claims" },
                { value: "promotions", text: "Promotions & Deals" }
            ],
            chatTypes: [
                { value: "chat", text: "Chat" },
                { value: "email", text: "Email" },
                { value: "none", text: "None" }
            ],
            form: new Form({
                id: "",
                category: "",
                subject: "",
                message: "",
                type: ""
            }),
            eform: new Form({
                id: "",
                category: "",
                subject: "",
                message: "",
                type: ""
            })
        };
    },
    methods: {
        getFaqsResults(page = 1) {
            this.$Progress.start();

            axios
                .get("/api/faqs?page=" + page)
                .then(
                    ({ data }) => (this.paginateFaqs = data.data.paginateFaqs)
                );

            this.$Progress.finish();
        },

        getFaqs(page = 1) {
            this.$Progress.start();

            axios
                .get("/api/faqs?page=" + page)
                .then(({ data }) => (this.faqs = data.data));

            this.$Progress.finish();
        },

        loadFaqsList() {
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/faqs")
                    .then(
                        ({ data }) => (
                            console.log(data.data.arrayFaqs),
                            (this.paginateFaqs = data.data.paginateFaqs),
                            (this.arrayFaqs = data.data.arrayFaqs)
                        )
                    );
            }
        },

        newModal() {
            this.editmode = false;
            this.form.reset();
            $("#addNew").modal("show");
        },

        createCategory() {
            this.$Progress.start();

            this.form
                .post("/api/storeFaqs")
                .then(data => {
                    $("#addNew").modal("hide");

                    Toast.fire({
                        icon: "success",
                        title: data.data.message
                    });
                    this.$Progress.finish();
                    this.loadFaqsList();
                })
                .catch(() => {
                    Toast.fire({
                        icon: "error",
                        title: "Some error occured! Please try again"
                    });
                });
        },

        updateCategory() {
            this.$Progress.start();
            this.eform
                .post("/api/editFaqs/" + this.eform.id)
                .then(response => {
                    // success
                    $("#editNew").modal("hide");
                    Toast.fire({
                        icon: "success",
                        title: response.data.message
                    });
                    this.$Progress.finish();
                    this.loadFaqsList();
                })
                .catch(() => {
                    this.$Progress.fail();
                });
        },

        deleteCategory(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then(result => {
                // Send request to the server
                if (result.value) {
                    this.form
                        .get("/api/deleteFaqs/" + id)
                        .then(() => {
                            Swal.fire(
                                "Deleted!",
                                "Faqs has been deleted.",
                                "success"
                            );
                            this.loadFaqsList();
                        })
                        .catch(data => {
                            Swal.fire("Failed!", data.message, "warning");
                        });
                }
            });
        },

        editModelFaq(faq) {
            this.editmode = true;
            this.eform.reset();
            this.eform.fill(faq);
            this.form.category = faq.category;
            this.form.type = faq.type;
            console.log(this.form.category);
            console.log(this.form.type);
            $("#editNew").modal("show");
        }
    },
    created() {
        this.$Progress.start();
        this.loadFaqsList();
        this.$Progress.finish();
    }
};
</script>
