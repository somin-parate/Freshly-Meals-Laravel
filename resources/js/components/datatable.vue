<template>
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Freshly Users List</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
      <table id="example1" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id">
            <td>{{ user.firstName }} {{ user.lastName }}</td>
            <td>{{ user.email }}</td>
            <td>{{ user.role }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- /.card-body -->
  </div>
</template>

<script>
export default {
  data() {
    return {
      users: [
        {
          firstName: "Frank",
          lastName: "Murphy",
          email: "frank.murphy@test.com",
          role: "User",
        },
        {
          firstName: "Vic",
          lastName: "Reynolds",
          email: "vic.reynolds@test.com",
          role: "Admin",
        },
        {
          firstName: "Gina",
          lastName: "Jabowski",
          email: "gina.jabowski@test.com",
          role: "Admin",
        },
        {
          firstName: "Jessi",
          lastName: "Glaser",
          email: "jessi.glaser@test.com",
          role: "User",
        },
        {
          firstName: "Jay",
          lastName: "Bilzerian",
          email: "jay.bilzerian@test.com",
          role: "User",
        },
      ],
    };
  },
  mounted() {
    $(function () {
      $("#example1").DataTable({
        paging: true,
        lengthChange: false,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
        responsive: true,
      });
    });
  },
};
</script>
