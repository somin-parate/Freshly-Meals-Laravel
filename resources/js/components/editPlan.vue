<template>
    <section class="content" data-select2-id="53">
        <div class="container-fluid" data-select2-id="52">
            <div class="card card-default" data-select2-id="51">
                <div class="card-header">
                    <h3 class="card-title">Update Meal Plan Type</h3>
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
                    @submit.prevent="updatePlanType"
                    enctype="multipart/form-data"
                >
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="planTypeName"
                                        placeholder="Enter title"
                                        v-model="form.title"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'title'
                                            )
                                        }"
                                    />
                                    <has-error
                                        :form="form"
                                        field="title"
                                    ></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Short Description</label>
                                    <textarea
                                        id="planTypeSDescription"
                                        class="form-control"
                                        rows="5"
                                        placeholder="Enter short description"
                                        v-model="form.short_description"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'short_description'
                                            )
                                        }"
                                    ></textarea>
                                    <has-error
                                        :form="form"
                                        field="short_description"
                                    ></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Long Description</label>
                                    <textarea
                                        id="planTypeLDescription"
                                        class="form-control"
                                        rows="5"
                                        placeholder="Enter long description"
                                        v-model="form.long_description"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'long_description'
                                            )
                                        }"
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
                                    <img
                                        v-bind:src="form.currentImage"
                                        style="margin-bottom: 15px; display: block"
                                    />
                                    <input
                                        type="file"
                                        class="form-control"
                                        id="planTypeImage"
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

                                <div class="form-group">
                                    <label>Upload Multple Cover Images</label>

                                    <div id="cover_images">
                                        <div
                                            v-for="image in form.cover_images"
                                            :class="'div-set cover-' + image.id"
                                        >
                                            <img
                                                v-bind:src="image.image"
                                                style="
                          margin-bottom: 20px;
                          display: block;
                          height: 200px;
                          width: 200px;
                          object-fit: cover;
                        "
                                            />
                                            <button
                                                type="button"
                                                class="btn btn-block btn-danger btn-set"
                                                :data-id="image.id"
                                                @click="
                                                    removeCoverImage($event)
                                                "
                                            >
                                                Remove
                                            </button>
                                        </div>
                                    </div>

                                    <input
                                        type="file"
                                        class="form-control"
                                        id="planTypeCoverImage"
                                        @change="uploadCoverImage"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'cover_image'
                                            )
                                        }"
                                        multiple
                                    />
                                    <has-error
                                        :form="form"
                                        field="cover_image"
                                    ></has-error>
                                </div>

                                <div class="form-group">
                                    <label>Shortcode ( Must be unique )</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="shortcode"
                                        placeholder="Enter Shortcode"
                                        v-model="form.shortcode"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'shortcode'
                                            )
                                        }"
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
                                    class="form-group"
                                    v-for="(input, k) in inputs"
                                    :key="k"
                                >
                                    <div class="row">
                                        <div class="col-md-2">
                                            <select
                                                name="meal_week"
                                                class="form-control"
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
                                                name="meal_day"
                                                class="form-control"
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
                                                name="meal_main"
                                                class="form-control"
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
                                                name="meal_snack"
                                                class="form-control"
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
                                                type="number"
                                                class="form-control"
                                                v-model="input.price"
                                                step="0.001"
                                                placeholder="Enter Price"
                                            />
                                        </div>
                                        <div class="col-md-2">
                                            <select
                                                name="gender"
                                                class="form-control"
                                                placeholder="Select Gender"
                                                v-model="input.gender"
                                            >
                                                <option value="selected"
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
                                                    class="fas fa-minus-circle"
                                                    @click="remove(k)"
                                                    v-show="
                                                        k ||
                                                            (!k &&
                                                                inputs.length >
                                                                    1)
                                                    "
                                                    >Remove</i
                                                >
                                                <i
                                                    class="fas fa-plus-circle"
                                                    @click="add(k)"
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
                        <button class="btn btn-primary">Update</button>
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
            planId: "",
            inputs: [],
            form: new Form({
                id: "",
                title: "",
                shortcode: "",
                image: "",
                cover_image: "",
                is_customized:"",
                short_description: "",
                long_description: "",
                currentImage: "",
                currentCoverImage: "",
                dynamic_prices: "",
                cover_images: [],
                new_cover_images: []
            })
        };
    },
    methods: {
        getPlanTypeImage(img) {
            return `/images/api_images/${img}`;
        },

        getCoverImage(img) {
            return `/images/plan_images/${img}`;
        },

        uploadCatImage(e) {
            let file = e.target.files[0];
            let reader = new FileReader();
            reader.onloadend = file => {
                this.form.image = reader.result;
            };
            reader.readAsDataURL(file);
        },

        uploadCoverImage(e) {
            this.form.new_cover_images = [];
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
                    self.form.new_cover_images.push(picFile.result);
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
                id: "",
                week: "",
                day: "",
                meal: "",
                snack: "",
                price: "",
                gender: ""
            });
        },

        remove(index) {
            this.inputs.splice(index, 1);
        },

        editModel(plan) {
            this.editmode = true;
            this.form.reset();
            this.form.fill(plan.plan);
            this.form.currentImage = this.getPlanTypeImage(plan.plan.image);
            this.form.currentCoverImage = this.getPlanTypeImage(
                plan.plan.cover_image
            );
            this.form.cover_images = this.loadCoverImages(
                plan.plan.plan_images
            );
            console.log(this.form.cover_images);
            this.loadPrices(plan.plan_variations);
        },

        loadCoverImages(images) {
            images.forEach(e => {
                e.image = this.getCoverImage(e.image);
            });
            return images;
        },

        removeCoverImage(e) {
            let imageId = e.target.dataset.id;
            let childDiv = e.currentTarget.parentElement;
            let parentDiv = childDiv.parentElement;
            parentDiv.removeChild(childDiv);

            axios
                .get("/api/removeCoverImage/" + imageId)
                .then(({ data }) => console.log("saddas"));
        },

        loadPrices(variations) {
            if (variations.length == 0) {
                this.inputs.push({
                    id: "",
                    week: "",
                    day: "",
                    meal: "",
                    snack: "",
                    price: "",
                    gender: ""
                });
            } else {
                variations.forEach(e => {
                    e.snacks = e.snacks === 0 ? "nosnack" : e.snacks;
                    this.inputs.push({
                        id: e.id,
                        week: e.weeks,
                        day: e.days,
                        meal: e.meals,
                        snack: e.snacks,
                        price: e.price,
                        gender: e.gender
                    });
                });
            }
        },

        updatePlanType() {
            this.form.dynamic_prices = JSON.stringify(this.inputs);
            // return;
            this.$Progress.start();
            this.form
                .put("/api/plan/" + this.form.id)
                .then(response => {
                    // success
                    Toast.fire({
                        icon: "success",
                        title: response.data.message
                    });
                    this.$router.push("/plans");
                    this.$Progress.finish();
                })
                .catch(() => {
                    this.$Progress.fail();
                });
        },

        getIDfromURL() {
            //Grab the path from your URL. In your case /writers/1/books/
            var path = window.location.pathname;

            //Break the path into segments
            var segments = path.split("/");

            //Return the segment that has the ID
            this.planId = segments[2];
            return this.planId;
        },

        backButton() {
            history.back();
        },

        loadPlan() {
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/plan/" + this.planId)
                    .then(({ data }) => this.editModel(data.data));
            }
        }
    },
    mounted() {
        console.log("Edit Paln Component mounted.");
    },

    created() {
        this.$Progress.start();
        this.getIDfromURL();
        this.loadPlan();
        this.$Progress.finish();
    }
};
</script>
<style>
.btn-set {
    margin-left: 50px;
    width: 100px;
}
.div-set {
    display: flex;
    align-items: center;
}
</style>
