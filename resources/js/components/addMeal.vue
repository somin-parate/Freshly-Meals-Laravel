<template>
    <section class="content" data-select2-id="53">
        <div class="container-fluid" data-select2-id="52">
            <div class="card card-default" data-select2-id="51">
                <div class="card-header">
                    <h3 class="card-title">Add Meal</h3>
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
                <form @submit.prevent="createMeal">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Select Plan</label>
                                            <select
                                                name="meal_plan_type"
                                                class="form-control"
                                                v-model="form.meal_plan_type"
                                                :class="{
                                                    'is-invalid': form.errors.has(
                                                        'meal_plan_type'
                                                    )
                                                }"
                                            >
                                                <option value=""
                                                    >Select Plan</option
                                                >
                                                <option
                                                    v-for="plan in plans"
                                                    :value="plan.id"
                                                >
                                                    {{ plan.title }}
                                                </option>
                                            </select>
                                            <has-error
                                                :form="form"
                                                field="meal_plan_type"
                                            ></has-error>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="typo__label"
                                                >Meal Date</label
                                            >
                                            <m-date-picker
                                                v-model="form.date"
                                                :multi="multi"
                                                :always-display="false"
                                                :format="formatDate"
                                            ></m-date-picker>
                                        </div>
                                        <!-- <div class="form-group">
                                    <label>Meal Name</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="meal_name"
                                        placeholder="Enter name"
                                        v-model="form.meal_name"
                                        :class="{ 'is-invalid': form.errors.has('meal_name') }"
                                    />
                                    <has-error :form="form" field="meal_name"></has-error>
                                </div> -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Meal Category</label>
                                            <select
                                                name="meal_category"
                                                class="form-control"
                                                v-model="form.meal_category"
                                                :class="{
                                                    'is-invalid': form.errors.has(
                                                        'meal_category'
                                                    )
                                                }"
                                            >
                                                <option value=""
                                                    >Select Category</option
                                                >
                                                <option value="Veg">Veg</option>
                                                <option value="Non-Veg"
                                                    >Non-Veg</option
                                                >
                                            </select>
                                            <has-error
                                                :form="form"
                                                field="meal_category"
                                            ></has-error>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Upload Meal Image</label>
                                            <input
                                                type="file"
                                                class="form-control"
                                                name="meal_image"
                                                @change="uploadMealImage"
                                                :class="{
                                                    'is-invalid': form.errors.has(
                                                        'meal_image'
                                                    )
                                                }"
                                            />
                                            <has-error
                                                :form="form"
                                                field="meal_image"
                                            ></has-error>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- type -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Meal Type</label>
                                            <select
                                                name="meal_type"
                                                class="form-control"
                                                v-model="form.meal_type"
                                                :class="{
                                                    'is-invalid': form.errors.has(
                                                        'meal_type'
                                                    )
                                                }"
                                            >
                                                <option value=""
                                                    >Select Type</option
                                                >
                                                <option value="Meal"
                                                    >Meal</option
                                                >
                                                <option value="Snack"
                                                    >Snack</option
                                                >
                                            </select>
                                            <has-error
                                                :form="form"
                                                field="meal_type"
                                            ></has-error>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- macros -->
                            <div class="col-md-12">
                                <label>Select Gender</label>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <select
                                                name="gender"
                                                class="form-control"
                                                v-model="form.gender"
                                                :class="{
                                                    'is-invalid': form.errors.has(
                                                        'gender'
                                                    )
                                                }"
                                            >
                                                <option value=""
                                                    >Select Gender</option
                                                >
                                                <option value="Male"
                                                    >Male</option
                                                >
                                                <option value="Female"
                                                    >Female</option
                                                >
                                            </select>
                                            <has-error
                                                :form="form"
                                                field="gender"
                                            ></has-error>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12"><label>Macros</label></div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <input
                                                type="text"
                                                value="Calories"
                                                class="form-control"
                                                readonly
                                            />
                                        </div>
                                        <div class="col-md-2">
                                            <input
                                                type="text"
                                                class="form-control"
                                                placeholder="Enter value"
                                                v-model="
                                                    form.macros[0].kcalsVal
                                                "
                                            />
                                        </div>
                                        <div class="col-md-2">
                                            <input
                                                type="text"
                                                class="form-control"
                                                placeholder="Enter unit"
                                                v-model="
                                                    form.macros[0].kcalsUnit
                                                "
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <input
                                                type="text"
                                                value="Protein"
                                                class="form-control"
                                                readonly
                                            />
                                        </div>
                                        <div class="col-md-2">
                                            <input
                                                type="text"
                                                class="form-control"
                                                placeholder="Enter value"
                                                v-model="
                                                    form.macros[1].proteinVal
                                                "
                                            />
                                        </div>
                                        <div class="col-md-2">
                                            <input
                                                type="text"
                                                class="form-control"
                                                placeholder="Enter unit"
                                                v-model="
                                                    form.macros[1].proteinUnit
                                                "
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <input
                                                type="text"
                                                value="Energy"
                                                class="form-control"
                                                readonly
                                            />
                                        </div>
                                        <div class="col-md-2">
                                            <input
                                                type="text"
                                                class="form-control"
                                                placeholder="Enter value"
                                                v-model="
                                                    form.macros[2].energyVal
                                                "
                                            />
                                        </div>
                                        <div class="col-md-2">
                                            <input
                                                type="text"
                                                class="form-control"
                                                placeholder="Enter unit"
                                                v-model="
                                                    form.macros[2].energyUnit
                                                "
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <input
                                                type="text"
                                                value="Carbs"
                                                class="form-control"
                                                readonly
                                            />
                                        </div>
                                        <div class="col-md-2">
                                            <input
                                                type="text"
                                                class="form-control"
                                                placeholder="Enter value"
                                                v-model="
                                                    form.macros[3].carbsVal
                                                "
                                            />
                                        </div>
                                        <div class="col-md-2">
                                            <input
                                                type="text"
                                                class="form-control"
                                                placeholder="Enter unit"
                                                v-model="
                                                    form.macros[3].carbsUnit
                                                "
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- end macros -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="typo__label"
                                                >Allergens</label
                                            >
                                            <multiselect
                                                v-model="value"
                                                tag-placeholder="Add this as new tag"
                                                placeholder="Select Allergens"
                                                label="name"
                                                track-by="code"
                                                :options="options"
                                                :close-on-select="false"
                                                :multiple="true"
                                                :taggable="true"
                                                @tag="addTag"
                                            ></multiselect>
                                            <!-- <pre class="language-json"><code>{{ value  }}</code></pre> -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="typo__label"
                                                >Food Items</label
                                            >
                                            <multiselect
                                                v-model="value1"
                                                tag-placeholder="Add this as new tag"
                                                placeholder="Select Food Items"
                                                label="name"
                                                track-by="code"
                                                :options="item_options"
                                                :close-on-select="false"
                                                :multiple="true"
                                                :taggable="true"
                                                @tag="addItemTag"
                                            ></multiselect>
                                            <!-- <pre class="language-json"><code>{{ value  }}</code></pre> -->
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
import Multiselect from "vue-multiselect";
import datetime from "vuejs-datetimepicker";
import mDatePicker from "./multiDatePicker.vue";

export default {
    components: {
        Multiselect,
        datetime,
        "m-date-picker": mDatePicker
    },
    data() {
        return {
            editmode: false,
            plans: {},
            food_items: {},
            multi: true,
            date: [],
            // date: '2021-10-21 07:24:06',
            value: [],
            value1: [],
            options: [],
            item_options: [],
            form: new Form({
                meal_plan_type: "",
                meal_name: "",
                meal_category: "",
                meal_type: "",
                meal_image: "",
                date: [],
                allergens_options: [],
                food_items: [],
                gender: "",
                dynamic_macros: [],
                macros: [
                    {
                        kcalsVal: "",
                        kcalsUnit: ""
                    },
                    {
                        proteinVal: "",
                        proteinUnit: ""
                    },
                    {
                        energyVal: "",
                        energyUnit: ""
                    },
                    {
                        carbsVal: "",
                        carbsUnit: ""
                    }
                ]
            })
        };
    },
    methods: {
        addTag(newTag) {
            const tag = {
                name: newTag,
                code:
                    newTag.substring(0, 2) +
                    Math.floor(Math.random() * 10000000)
            };
            this.options.push(tag);
            this.value.push(tag);
        },
        formatDate(data) {
            return data.toLocaleDateString();
        },

        backButton() {
            history.back();
        },

        addItemTag(newTag) {
            const tag = {
                name: newTag,
                code:
                    newTag.substring(0, 2) +
                    Math.floor(Math.random() * 10000000)
            };
            this.item_options.push(tag);
            this.value1.push(tag);
        },

        uploadMealImage(e) {
            let file = e.target.files[0];
            let reader = new FileReader();
            reader.onloadend = file => {
                this.form.meal_image = reader.result;
            };
            reader.readAsDataURL(file);
        },

        loadPlanTypes() {
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/planTypes")
                    .then(({ data }) => (this.plans = data.data));
            }
        },

        getMealImage(img) {
            return `/images/api_images/${img}`;
        },

        loadAllergens() {
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/allergensList")
                    .then(({ data }) => this.bindAllergens(data.data));
            }
        },

        bindAllergens(allergens) {
            allergens.forEach(e => {
                var object = {};
                object = { name: e.title, code: e.id };
                this.options.push(object);
            });
        },

        bindFoodItems(items) {
            items.forEach(e => {
                var object1 = {};
                object1 = { name: e.title, code: e.id };
                this.item_options.push(object1);
            });
        },

        getSelectedOptions() {
            var final_options = [];
            this.value.forEach(e => {
                final_options.push(e.code);
            });
            this.form.allergens_options = final_options;
        },

        getSelectedItems() {
            var finalitem_options = [];
            this.value1.forEach(e => {
                finalitem_options.push(e.code);
            });
            this.form.food_items = finalitem_options;
        },

        bindDateTime() {
            var datetime = $("#tj-datetime-input")
                .val()
                .toString();
            this.form.meal_begin_at = datetime;
        },

        createMeal() {
            this.$Progress.start();
            this.form.dynamic_macros = JSON.stringify(this.form.macros);
            this.getSelectedOptions();
            this.getSelectedItems();
            this.form
                .post("/api/meal")
                .then(data => {
                    Toast.fire({
                        icon: "success",
                        title: data.data.message
                    });
                    this.$router.push("/meals");
                    this.$Progress.finish();
                })
                .catch(() => {
                    Toast.fire({
                        icon: "error",
                        title: "Some error occured! Please try again"
                    });
                });
        },

        loadFoodItems() {
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/foodItemsList")
                    .then(({ data }) => this.bindFoodItems(data.data));
            }
        }
    },

    mounted() {
        $(function() {
            $(".select2").select2();
        });
    },

    created() {
        this.$Progress.start();
        this.loadPlanTypes();
        this.loadAllergens();
        this.loadFoodItems();
        this.$Progress.finish();
    }
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
