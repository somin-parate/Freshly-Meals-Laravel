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
                                    placeholder="Search Users..."
                                    id="search-input"
                                    v-model="keywords"
                                    class="form-control"
                                />
                            </div>
                            <!-- <div
                class="form-group"
                style="
                  padding-left: 7%;
                  padding-top: 0px;
                  /* background: blueviolet; */
                "
              >
                <button
                  style="
                    background: #38c172;
                    color: white;
                    border-radius: 4px;
                    height: 35px;
                    width: 84px;
                  "
                  @click="reset"
                >
                  Reset
                </button>
              </div> -->
                        </div>

                        <div class="card-header">
                            <h3 class="card-title">Freshly Users List</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Gender</th>
                                        <th>Email Verified ?</th>
                                        <th>Book Nutritionist ?</th>
                                        <th>Cutlery ?</th>
                                        <th>Created</th>
                                        <th>View Profile</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="category in categories.data"
                                        :key="category.user_id"
                                    >
                                        <td>{{ category.user_id }}</td>
                                        <td class="text-capitalize">
                                            {{ category.fname }}
                                        </td>
                                        <td>{{ category.email }}</td>
                                        <td>{{ category.gender }}</td>
                                        <td>
                                            <i
                                                v-if="category.status == '1'"
                                                class="fa fa-check green"
                                            ></i>
                                            <i
                                                v-else
                                                class="fas fa-times red"
                                                aria-hidden="true"
                                            ></i>
                                        </td>
                                        <td>
                                            <i
                                                v-if="
                                                    category.book_nutritionist ==
                                                        'true'
                                                "
                                                class="fa fa-check green"
                                            ></i>
                                            <i
                                                v-else
                                                class="fas fa-times red"
                                                aria-hidden="true"
                                            ></i>
                                        </td>
                                        <td>
                                            <i
                                                v-if="
                                                    category.add_cutlery ==
                                                        'true'
                                                "
                                                class="fa fa-check green"
                                            ></i>
                                            <i
                                                v-else
                                                class="fas fa-times red"
                                                aria-hidden="true"
                                            ></i>
                                        </td>
                                        <td>
                                            {{
                                                formatDate(category.created_at)
                                            }}
                                        </td>
                                        <td>
                                            <router-link
                                                class="btn btn-success"
                                                v-bind:to="
                                                    '/freshly_users/' +
                                                        category.user_id +
                                                        '/view'
                                                "
                                                ><i
                                                    class="fa fa-eye"
                                                    aria-hidden="true"
                                                ></i></router-link
                                            >/
                                            <a
                                                href="#"
                                                @click="
                                                    deleteUser(category.user_id)
                                                "
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
                                :data="categories"
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
        </div>
    </section>
</template>

<script>
export default {
    data() {
        return {
            editmode: false,
            categories: {},
            allUsers: {},
            keywords: null,
            items: "",
            tax: null
        };
    },
    methods: {
        formatDate(dateString) {
            const options = { year: "numeric", month: "long", day: "numeric" };
            return new Date(dateString).toLocaleDateString(undefined, options);
        },

        getResults(page = 1) {
            this.$Progress.start();

            axios
                .get("/api/freshlyUsers?page=" + page)
                .then(({ data }) => (this.categories = data.data.users));

            this.$Progress.finish();
        },

        loadExerciseCategories() {
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/freshlyUsers")
                    .then(
                        ({ data }) => (
                            (this.categories = data.data.users),
                            (this.allUsers = data.data.usersArray)
                        )
                    );
            }
        },

        deleteUser(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then(result => {
                if (result.value) {
                    axios
                        .post("api/deleteUser/" + id)
                        .then(() => {
                            Swal.fire(
                                "Deleted!",
                                "User has been deleted.",
                                "success"
                            );
                            this.loadExerciseCategories();
                        })
                        .catch(data => {
                            Swal.fire("Failed!", data.message, "warning");
                        });
                }
            });
        },

        // loadUsers() {
        //   if (this.$gate.isAdmin()) {
        //     axios
        //       .get("/api/freshlyUsersWithoutPaginate")
        //       .then(({ data }) => (this.usersArray = data.data));
        //   }
        // },

        fetch() {
            axios
                .post("/api/search-items", {
                    keyword: this.keywords,
                    categories: this.allUsers
                })
                .then(
                    ({ data }) => (
                        (this.categories = {}), (this.categories = data.data)
                    )
                );
        },

        reset() {
            document.location.reload(true);
        }
    },
    watch: {
        keywords: function keywords(after, before) {
            this.fetch();
        }
    },
    mounted() {
        console.log("Component mounted.");
    },
    created() {
        this.$Progress.start();
        this.loadExerciseCategories();
        // this.loadUsers();
        this.$Progress.finish();
    }
};
</script>

<style lang="scss">
.text-capitalize {
    text-transform: uppercase !important;
}
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
