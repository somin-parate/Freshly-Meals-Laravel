<template>
    <section class="content" data-select2-id="53">
        <div class="container-fluid" data-select2-id="52">
            <div class="card card-default" data-select2-id="51">
                <div class="card-header">
                    <h3 class="card-title">Add Meal Plan Type</h3>
                    <div style="float: right">
                        <button
                            class="btn btn-danger"
                            style="float: right;"
                            @click="backButton()"
                        >
                            Back
                        </button>
                    </div>
                </div>
                <form
                    @submit.prevent="createPlanType"
                    enctype="multipart/form-data"
                >
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'title'
                                            )
                                        }"
                                        class="form-control"
                                        id="planTypeName"
                                        placeholder="Enter title"
                                        type="text"
                                        v-model="form.title"
                                    />
                                    <has-error
                                        :form="form"
                                        field="title"
                                    ></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Short Description</label>
                                    <textarea
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'short_description'
                                            )
                                        }"
                                        class="form-control"
                                        id="planTypeSDescription"
                                        placeholder="Enter short description"
                                        rows="5"
                                        v-model="form.short_description"
                                    ></textarea>
                                    <has-error
                                        :form="form"
                                        field="short_description"
                                    ></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Long Description</label>
                                    <textarea
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'long_description'
                                            )
                                        }"
                                        class="form-control"
                                        id="planTypeLDescription"
                                        placeholder="Enter long description"
                                        rows="5"
                                        v-model="form.long_description"
                                    ></textarea>
                                    <has-error
                                        :form="form"
                                        field="long_description"
                                    ></has-error>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload Thumb Image</label>
                                    <input
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'image'
                                            )
                                        }"
                                        @change="uploadCatImage"
                                        class="form-control"
                                        id="planTypeImage"
                                        type="file"
                                    />
                                    <has-error
                                        :form="form"
                                        field="image"
                                    ></has-error>
                                </div>

                                <div class="form-group">
                                    <label>Upload Multple Cover Images</label>
                                    <input
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'cover_image'
                                            )
                                        }"
                                        @change="uploadCoverImage"
                                        class="form-control"
                                        id="planTypeCoverImage"
                                        type="file"
                                        multiple
                                    />
                                    <has-error
                                        :form="form"
                                        field="cover_image"
                                    ></has-error>
                                </div>
                                <div id="result"></div>

                                <div class="form-group">
                                    <label>Shortcode</label>
                                    <input
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'shortcode'
                                            )
                                        }"
                                        class="form-control"
                                        id="shortcode"
                                        placeholder="Enter Shortcode"
                                        type="text"
                                        v-model="form.shortcode"
                                    />
                                    <has-error
                                        :form="form"
                                        field="shortcode"
                                    ></has-error>
                                </div>

                                <div class="form-group">
                                    <label>Is Customized Plan?</label>
                                    <select
                                        name="is_customized"
                                        class="form-control"
                                        v-model="form.is_customized"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'is_customized'
                                            )
                                        }"
                                    >
                                        <option value="" selected
                                            >Select</option
                                        >
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                    <has-error
                                        :form="form"
                                        field="is_customized"
                                    ></has-error>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label>Dynamic Price</label>
                            </div>
                            <div class="col-md-12">
                                <div
                                    :key="k"
                                    class="form-group"
                                    v-for="(input, k) in inputs"
                                >
                                    <div class="row">
                                        <div class="col-md-2">
                                            <select
                                                class="form-control"
                                                name="meal_week"
                                                v-model="input.week"
                                            >
                                                <option value=""
                                                    >Select Week</option
                                                >
                                                <option value="1"
                                                    >1 Week</option
                                                >
                                                <option value="2"
                                                    >2 Week</option
                                                >
                                                <option value="3"
                                                    >3 Week</option
                                                >
                                                <option value="4"
                                                    >4 Week</option
                                                >
                                                <option value="8"
                                                    >8 Week</option
                                                >
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <select
                                                class="form-control"
                                                name="meal_day"
                                                v-model="input.day"
                                            >
                                                <option value=""
                                                    >Select Day</option
                                                >
                                                <option value="3">3 Day</option>
                                                <option value="4">4 Day</option>
                                                <option value="5">5 Day</option>
                                                <option value="6">6 Day</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <select
                                                class="form-control"
                                                name="meal_main"
                                                v-model="input.meal"
                                            >
                                                <option value=""
                                                    >Select Meal</option
                                                >
                                                <option value="1"
                                                    >1 Meal</option
                                                >
                                                <option value="2"
                                                    >2 Meal</option
                                                >
                                                <option value="3"
                                                    >3 Meal</option
                                                >
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <select
                                                class="form-control"
                                                name="meal_snack"
                                                v-model="input.snack"
                                            >
                                                <option value=""
                                                    >Select Snack</option
                                                >
                                                <option value="nosnack"
                                                    >0 Snack</option
                                                >
                                                <option value="1"
                                                    >1 Snack</option
                                                >
                                                <option value="2"
                                                    >2 Snack</option
                                                >
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <input
                                                class="form-control"
                                                placeholder="Enter Price"
                                                type="number"
                                                v-model="input.price"
                                            />
                                        </div>
                                        <div class="col-md-2">
                                            <select
                                                name="gender"
                                                class="form-control"
                                                v-model="input.gender"
                                            >
                                                <option selected
                                                    >Select Gender</option
                                                >
                                                <option value="Male"
                                                    >Male</option
                                                >
                                                <option value="Female"
                                                    >Female</option
                                                >
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <span>
                                                <i
                                                    @click="remove(k)"
                                                    class="fas fa-minus-circle"
                                                    v-show="
                                                        k ||
                                                            (!k &&
                                                                inputs.length >
                                                                    1)
                                                    "
                                                    >Remove</i
                                                >
                                                <i
                                                    @click="add(k)"
                                                    class="fas fa-plus-circle"
                                                    v-show="
                                                        k == inputs.length - 1
                                                    "
                                                    >Add New Field</i
                                                >
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary">Submit</button>
                    </div>
                </form>
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
            images: [],
            inputs: [
                {
                    week: "",
                    day: "",
                    meal: "",
                    snack: "",
                    price: "",
                    gender: ""
                }
            ],
            form: new Form({
                title: "",
                image: "",
                shortcode: "",
                cover_image: [],
                short_description: "",
                is_customized: "",
                long_description: "",
                dynamic_prices: ""
            })
        };
    },
    methods: {
        uploadCatImage(e) {
            let file = e.target.files[0];
            let reader = new FileReader();
            reader.onloadend = file => {
                this.form.image = reader.result;
            };
            reader.readAsDataURL(file);
        },

        uploadCoverImage(e) {
            this.form.cover_image = [];
            var self = this;
            var files = e.target.files; //FileList object
            // var output = document.getElementById("result");
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                //Only pics
                if (!file.type.match("image")) continue;
                var picReader = new FileReader();
                picReader.addEventListener("load", function(event) {
                    var picFile = event.target;
                    self.form.cover_image.push(picFile.result);
                    // var div = document.createElement("div");
                    // div.innerHTML =
                    //   "<img class='thumbnail' src='" +
                    //   picFile.result +
                    //   "'" +
                    //   "title='" +
                    //   picFile.name +
                    //   "'/>";
                    // output.insertBefore(div, null);
                });
                //Read the image
                picReader.readAsDataURL(file);
            }
        },

        add() {
            this.inputs.push({
                week: "",
                day: "",
                meal: "",
                snack: "",
                price: ""
            });
        },

        remove(index) {
            this.inputs.splice(index, 1);
        },

        backButton() {
            history.back();
        },

        createPlanType() {
            this.$Progress.start();
            this.form.dynamic_prices = JSON.stringify(this.inputs);
            this.form
                .post("/api/plan")
                .then(data => {
                    Toast.fire({ icon: "success", title: data.data.message });
                    this.$router.push("/plans");
                    this.$Progress.finish();
                })
                .catch(() => {
                    Toast.fire({
                        icon: "error",
                        title: "Some error occured! Please try again"
                    });
                });
        }
    },
    mounted() {
        console.log("Component mounted.");
    }
};
</script>
