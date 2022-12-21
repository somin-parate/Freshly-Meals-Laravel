<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card" v-if="$gate.isAdmin()">
                        <div class="card-header">
                            <h3 class="card-title">Notifications List</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Title</th>
                                        <th>Message</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="notification in notifications.data"
                                        :key="notification.id"
                                    >
                                        <td>
                                            <router-link
                                                v-bind:to="
                                                    '/freshly_users/' +
                                                        notification.user_id +
                                                        '/view'
                                                "
                                                >{{
                                                    notification.name
                                                }}</router-link
                                            >
                                        </td>
                                        <td>{{ notification.title }}</td>
                                        <td>{{ notification.message }}</td>
                                        <td>{{ notification.date }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <pagination
                                :data="notifications"
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
        </div>
    </section>
</template>

<script>
export default {
    data() {
        return {
            editmode: false,
            notifications: {}
        };
    },
    methods: {
        getResults(page = 1) {
            this.$Progress.start();

            axios
                .get("/api/notifications?page=" + page)
                .then(({ data }) => (this.notifications = data.data));

            this.$Progress.finish();
        },

        loadNotificationsList() {
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/notifications")
                    .then(({ data }) => (this.notifications = data.data));
            }
        }
    },
    mounted() {
        console.log("Component mounted.");
    },
    created() {
        this.$Progress.start();
        this.loadNotificationsList();
        this.$Progress.finish();
    }
};
</script>
