<template>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card" v-if="$gate.isAdmin()">
            <div class="card-header">
              <h3 class="card-title">Delivery Timeslots</h3>

              <div class="card-tools">
                <button type="button" class="btn btn-sm btn-primary" @click="newModal">
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
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="slot in slots.data" :key="slot.id">
                    <td>{{ slot.id }}</td>
                    <td class="text-capitalize">{{ slot.start_time }}</td>
                    <td class="text-capitalize">{{ slot.end_time }}</td>
                    <td>
                      <a href="#" @click="editModel(slot)">
                        <i class="fa fa-edit blue"></i>
                      </a>
                      <a href="#" @click="deleteSlot(slot.id)">
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
                :data="slots"
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
              <h5 class="modal-title">Add New Timeslot</h5>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <form @submit.prevent="createSlot">
              <div class="modal-body">
                <div class="form-group">
                  <label>Start Time</label><br/>
                  <vue-timepicker format="hh:mm A" v-model="form.start_time" id="slot_start" close-on-complete placeholder="Pick start time"></vue-timepicker>
                </div>
                <div class="form-group">
                  <label>End Time</label><br/>
                  <vue-timepicker format="hh:mm A" v-model="form.end_time" id="slot_end" close-on-complete placeholder="Pick end time"></vue-timepicker>
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
                <button type="submit" class="btn btn-primary">Create</button>
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
        aria-labelledby="addNew"
        aria-hidden="true"
      >
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Edit Timeslot</h5>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <form @submit.prevent="updateSlot()">
              <div class="modal-body">
                <div class="form-group">
                  <label>Start Time</label><br/>
                  <vue-timepicker format="hh:mm A" v-model="eform.start_time" id="slot_start" close-on-complete placeholder="Pick start time"></vue-timepicker>
                </div>
                <div class="form-group">
                  <label>End Time</label><br/>
                  <vue-timepicker format="hh:mm A" v-model="eform.end_time" id="slot_end" close-on-complete placeholder="Pick end time"></vue-timepicker>
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
                <button type="submit" class="btn btn-primary">Update</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <!-- Edit Modal -->
    </div>
  </section>
</template>

<script>
import VueTimepicker from 'vue2-timepicker/src/vue-timepicker.vue';

export default {
  components: { VueTimepicker },
  data() {
    return {
      editmode: false,
      slots: {},
      form: new Form({
        start_time: "",
        end_time: "",
      }),
      eform: new Form({
        id: "",
        start_time: "",
        end_time: "",
      }),
    };
  },
  methods: {
    getResults(page = 1) {
      this.$Progress.start();

      axios
        .get("/api/slots?page=" + page)
        .then(({ data }) => (this.slots = data.data));

      this.$Progress.finish();
    },

    loadSlots() {
      if (this.$gate.isAdmin()) {
        axios
          .get("/api/slots")
          .then(({ data }) => (this.slots = data.data));
      }
    },

    newModal() {
      this.editmode = false;
      this.form.reset();
      $('#slot_start, #slot_end').val("");
      $("#addNew").modal("show");
    },

    createSlot() {
      this.$Progress.start();
      this.form
        .post("/api/addSlot")
        .then((data) => {
          $("#addNew").modal("hide");

          Toast.fire({
            icon: "success",
            title: data.data.message,
          });
          this.$Progress.finish();
          this.loadSlots();
        })
        .catch(() => {
          Toast.fire({
            icon: "error",
            title: "Some error occured! Please try again",
          });
        });
    },

    editModel(slot) {
      this.editmode = true;
      this.eform.reset();
      this.eform.fill(slot);
      $("#editNew").modal("show");
    },

    updateSlot() {
      this.$Progress.start();
      this.eform
        .put("/api/editSlot/" + this.eform.id)
        .then((response) => {
          // success
          $("#editNew").modal("hide");
          Toast.fire({
            icon: "success",
            title: response.data.message,
          });
          this.$Progress.finish();
          this.loadSlots();
        })
        .catch(() => {
          this.$Progress.fail();
        });
    },

    deleteSlot(id) {
      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
      }).then((result) => {
        if (result.value) {
          this.form
            .delete("/api/deleteSlot/" + id)
            .then(() => {
              Swal.fire("Deleted!", "Slot has been deleted.", "success");
              this.loadSlots();
            })
            .catch((data) => {
              Swal.fire("Failed!", data.message, "warning");
            });
        }
      });
    },
  },

  created() {
    this.$Progress.start();
    this.loadSlots();
    this.$Progress.finish();
  },
};
</script>
