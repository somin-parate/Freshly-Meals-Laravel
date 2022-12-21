<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <!-- Profile Image -->
                <div class="card card-primary card-outline">
                    <div class="edit-icon float-right" style="">
                        <a href="#" @click="editModelUser(profile)">
                            <i
                                class="fa fa-edit nav-icon cyan"
                                style="float: right;margin-top: 10px;margin-right: 10px;"
                            ></i
                        ></a>
                    </div>
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <img
                                class="profile-user-img img-fluid img-circle"
                                v-bind:src="getUserImage(profile.image)"
                                alt="User profile picture"
                            />
                        </div>
                        <h3 class="profile-username text-center">
                            {{ profile.fname }}
                        </h3>
                        <p class="text-muted text-center">
                            {{ profile.email }}
                        </p>
                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Mobile</b>
                                <a class="float-right">{{
                                    profile.mobile_number
                                }}</a>
                            </li>
                            <!-- <li class="list-group-item">
                                <b>Emirate Id</b>
                                <a class="float-right">{{
                                    profile.emirate_id
                                }}</a>
                            </li> -->
                            <li class="list-group-item">
                                <b>Gender</b>
                                <a class="float-right">{{ profile.gender }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>DOB</b>
                                <a class="float-right">{{ profile.dob }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Height</b>
                                <a class="float-right"
                                    >{{ profile.height }} cm</a
                                >
                            </li>
                            <li class="list-group-item">
                                <b>Weight</b>
                                <a class="float-right"
                                    >{{ profile.weight }} kg</a
                                >
                            </li>
                            <li class="list-group-item">
                                <b>Blood Group</b>
                                <a class="float-right">{{
                                    profile.blood_group
                                }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Allergies</b>
                                <a class="float-right">{{
                                    profile.allergies
                                }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Cardio</b>
                                <a class="float-right">{{ profile.cardio }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Medical Conditions</b>
                                <a class="float-right">{{
                                    profile.med_conditions
                                }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Book Nutritionist?</b>
                                <div
                                    v-if="profile.book_status == 'true'"
                                    class="float-right"
                                >
                                    <i class="fa fa-check green ">Consulted</i>
                                </div>
                                <div v-else>
                                    <button
                                        v-if="
                                            profile.book_nutritionist == 'true'
                                        "
                                        type="button"
                                        class="btn btn-success float-right"
                                        @click="samplefunction()"
                                        style="margin-top: -25px"
                                    >
                                        <i
                                            class="fas fa-circle"
                                            aria-hidden="true"
                                        ></i>
                                        Done?
                                    </button>

                                    <i
                                        v-else
                                        class="fas fa-times red float-right"
                                        aria-hidden="true"
                                    ></i>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <b>Cutlery?</b>
                                <i
                                    v-if="profile.cutlery == 'true'"
                                    class="fa fa-check green float-right"
                                ></i>
                                <i
                                    v-else
                                    class="fas fa-times red float-right"
                                    aria-hidden="true"
                                ></i>
                            </li>
                        </ul>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a
                                    class="nav-link active"
                                    href="#activity"
                                    data-toggle="tab"
                                    >Active Plan</a
                                >
                            </li>
                            <li class="nav-item">
                                <a
                                    href="#past_subs"
                                    data-toggle="tab"
                                    class="nav-link"
                                    >Past Plans</a
                                >
                            </li>
                            <li class="nav-item">
                                <a
                                    href="#upcoming_subs"
                                    data-toggle="tab"
                                    class="nav-link"
                                    >Upcoming Plan</a
                                >
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    href="#timeline"
                                    data-toggle="tab"
                                    >My Meals</a
                                >
                            </li>
                            <!-- <li class="nav-item">
                                <a
                                    class="nav-link"
                                    href="#myMeals"
                                    data-toggle="tab"
                                    >My Meals</a
                                >
                            </li> -->
                            <li class="nav-item">
                                <a
                                    href="#feedback"
                                    data-toggle="tab"
                                    class="nav-link"
                                    >My Feedbacks</a
                                >
                            </li>
                            <li class="nav-item">
                                <a
                                    href="#transaction"
                                    data-toggle="tab"
                                    class="nav-link"
                                    >My Transactions</a
                                >
                            </li>
                            <li class="nav-item">
                                <a
                                    href="#notification"
                                    data-toggle="tab"
                                    class="nav-link"
                                    >My Notifications</a
                                >
                            </li>
                            <li class="nav-item">
                                <a
                                    href="#notes"
                                    data-toggle="tab"
                                    class="nav-link"
                                    >Notes</a
                                >
                            </li>
                            <!-- <li class="nav-item">
                        <a
                            href="#bug"
                            data-toggle="tab"
                            class="nav-link"
                            >My Bugs</a
                        >
                        </li> -->
                        </ul>
                        <div style="float: right;margin-top: -38px;">
                            <button
                                class="btn btn-danger"
                                style="float: right;"
                                @click="backButton()"
                            >
                                Back
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="active tab-pane" id="activity">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div
                                            v-if="plan_status === true"
                                            class="card card-row card-primary"
                                        >
                                            <div class="card-header bg-success">
                                                <h3 class="card-title">
                                                    {{ plan_details.plan_name }}
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div
                                                        class="col-12 col-md-12 col-lg-12 order-2 order-md-1"
                                                    >
                                                        <div class="row">
                                                            <div
                                                                class="col-12 col-sm-3"
                                                            >
                                                                <div
                                                                    class="info-box bg-light"
                                                                >
                                                                    <div
                                                                        class="info-box-content"
                                                                    >
                                                                        <span
                                                                            class="info-box-text text-center text-muted"
                                                                            >Weeks</span
                                                                        >
                                                                        <span
                                                                            class="info-box-number text-center text-muted mb-0"
                                                                            >{{
                                                                                plan_details.week
                                                                            }}
                                                                            Week</span
                                                                        >
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div
                                                                class="col-12 col-sm-3"
                                                            >
                                                                <div
                                                                    class="info-box bg-light"
                                                                >
                                                                    <div
                                                                        class="info-box-content"
                                                                    >
                                                                        <span
                                                                            class="
                                                         info-box-text
                                                         text-center text-muted
                                                         "
                                                                            >Days
                                                                            per
                                                                            week</span
                                                                        >
                                                                        <span
                                                                            class="
                                                         info-box-number
                                                         text-center text-muted
                                                         mb-0
                                                         "
                                                                            >{{
                                                                                plan_details.day
                                                                            }}
                                                                            Days</span
                                                                        >
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div
                                                                class="col-12 col-sm-3"
                                                            >
                                                                <div
                                                                    class="info-box bg-light"
                                                                >
                                                                    <div
                                                                        class="info-box-content"
                                                                    >
                                                                        <span
                                                                            class="info-box-text text-center text-muted"
                                                                            >Meal
                                                                            per
                                                                            day?</span
                                                                        >
                                                                        <span
                                                                            class="info-box-number text-center text-muted mb-0 "
                                                                            >{{
                                                                                plan_details.meal
                                                                            }}
                                                                            Meal</span
                                                                        >
                                                                    </div>
                                                                    <div
                                                                        class="edit-icon"
                                                                        style=""
                                                                    >
                                                                        <a
                                                                            href="#"
                                                                            @click="
                                                                                editModelMeal(
                                                                                    plan_details.meal
                                                                                )
                                                                            "
                                                                        >
                                                                            <i
                                                                                class="fa fa-edit nav-icon cyan"
                                                                            ></i
                                                                        ></a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div
                                                                class="col-12 col-sm-3"
                                                            >
                                                                <div
                                                                    class="info-box bg-light"
                                                                >
                                                                    <div
                                                                        class="info-box-content"
                                                                    >
                                                                        <span
                                                                            class="
                                                         info-box-text
                                                         text-center text-muted
                                                         "
                                                                            >Snack</span
                                                                        >
                                                                        <span
                                                                            class="
                                                         info-box-number
                                                         text-center text-muted
                                                         mb-0
                                                         "
                                                                            >{{
                                                                                plan_details.snack
                                                                            }}
                                                                            Snack</span
                                                                        >
                                                                    </div>
                                                                    <div
                                                                        class="edit-icon"
                                                                        style=""
                                                                    >
                                                                        <a
                                                                            href="#"
                                                                            @click="
                                                                                editModelSnack(
                                                                                    plan_details.snack
                                                                                )
                                                                            "
                                                                        >
                                                                            <i
                                                                                class="fa fa-edit nav-icon cyan"
                                                                            ></i
                                                                        ></a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="card card-light card-outline"
                                                >
                                                    <div class="card-header">
                                                        <h5 class="card-title">
                                                            Order Details
                                                        </h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div
                                                                class="col-md-3"
                                                            >
                                                                <img
                                                                    v-bind:src="
                                                                        getPlanTypeImage(
                                                                            plan_details.image
                                                                        )
                                                                    "
                                                                    style="object-fit: cover"
                                                                />
                                                            </div>
                                                            <div
                                                                class="col-md-9"
                                                            >
                                                                <div
                                                                    class="table-responsive"
                                                                >
                                                                    <table
                                                                        class="table"
                                                                    >
                                                                        <tbody>
                                                                            <tr>
                                                                                <th
                                                                                    style="width: 50%"
                                                                                >
                                                                                    Meal
                                                                                    Start
                                                                                    Date:
                                                                                </th>
                                                                                <td>
                                                                                    {{
                                                                                        plan_details.start_date
                                                                                    }}
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th
                                                                                    style="width: 50%"
                                                                                >
                                                                                    Order
                                                                                    Date:
                                                                                </th>
                                                                                <td>
                                                                                    {{
                                                                                        plan_details.order_date
                                                                                    }}
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th
                                                                                    style="width: 50%"
                                                                                >
                                                                                    Order
                                                                                    Number:
                                                                                </th>
                                                                                <td>
                                                                                    {{
                                                                                        plan_details.order_number
                                                                                    }}
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th>
                                                                                    Grand
                                                                                    Total:
                                                                                </th>
                                                                                <td>
                                                                                    AED
                                                                                    {{
                                                                                        plan_details.grand_total
                                                                                    }}
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else>No active plan found</div>
                                        <!-- /.card -->
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="past_subs">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div
                                            v-if="past_plan_status === true"
                                            class="card card-row card-primary"
                                        >
                                            <div
                                                v-for="past in past_plan_details"
                                                :key="past.id"
                                            >
                                                <div
                                                    class="card-header bg-success"
                                                >
                                                    <h3 class="card-title">
                                                        {{ past.plan_name }}
                                                    </h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div
                                                            class="col-12 col-md-12 col-lg-12 order-2 order-md-1"
                                                        >
                                                            <div class="row">
                                                                <div
                                                                    class="col-12 col-sm-3"
                                                                >
                                                                    <div
                                                                        class="info-box bg-light"
                                                                    >
                                                                        <div
                                                                            class="info-box-content"
                                                                        >
                                                                            <span
                                                                                class="info-box-text text-center text-muted"
                                                                                >Weeks</span
                                                                            >
                                                                            <span
                                                                                class="info-box-number text-center text-muted mb-0"
                                                                                >{{
                                                                                    past.week
                                                                                }}
                                                                                Week</span
                                                                            >
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div
                                                                    class="col-12 col-sm-3"
                                                                >
                                                                    <div
                                                                        class="info-box bg-light"
                                                                    >
                                                                        <div
                                                                            class="info-box-content"
                                                                        >
                                                                            <span
                                                                                class="
                                                         info-box-text
                                                         text-center text-muted
                                                         "
                                                                                >Days
                                                                                per
                                                                                week</span
                                                                            >
                                                                            <span
                                                                                class="
                                                         info-box-number
                                                         text-center text-muted
                                                         mb-0
                                                         "
                                                                                >{{
                                                                                    past.day
                                                                                }}
                                                                                Days</span
                                                                            >
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div
                                                                    class="col-12 col-sm-3"
                                                                >
                                                                    <div
                                                                        class="info-box bg-light"
                                                                    >
                                                                        <div
                                                                            class="info-box-content"
                                                                        >
                                                                            <span
                                                                                class="info-box-text text-center text-muted"
                                                                                >Meal
                                                                                per
                                                                                day?</span
                                                                            >
                                                                            <span
                                                                                class="info-box-number text-center text-muted mb-0 "
                                                                                >{{
                                                                                    past.meal
                                                                                }}
                                                                                Meal</span
                                                                            >
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div
                                                                    class="col-12 col-sm-3"
                                                                >
                                                                    <div
                                                                        class="info-box bg-light"
                                                                    >
                                                                        <div
                                                                            class="info-box-content"
                                                                        >
                                                                            <span
                                                                                class="
                                                         info-box-text
                                                         text-center text-muted
                                                         "
                                                                                >Snack</span
                                                                            >
                                                                            <span
                                                                                class="
                                                         info-box-number
                                                         text-center text-muted
                                                         mb-0
                                                         "
                                                                                >{{
                                                                                    past.snack
                                                                                }}
                                                                                Snack</span
                                                                            >
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="card card-light card-outline"
                                                    >
                                                        <div
                                                            class="card-header"
                                                        >
                                                            <h5
                                                                class="card-title"
                                                            >
                                                                Order Details
                                                            </h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div
                                                                    class="col-md-3"
                                                                >
                                                                    <img
                                                                        v-bind:src="
                                                                            getPlanTypeImage(
                                                                                past.image
                                                                            )
                                                                        "
                                                                        style="object-fit: cover"
                                                                    />
                                                                </div>
                                                                <div
                                                                    class="col-md-9"
                                                                >
                                                                    <div
                                                                        class="table-responsive"
                                                                    >
                                                                        <table
                                                                            class="table"
                                                                        >
                                                                            <tbody>
                                                                                <tr>
                                                                                    <th
                                                                                        style="width: 50%"
                                                                                    >
                                                                                        Meal
                                                                                        Start
                                                                                        Date:
                                                                                    </th>
                                                                                    <td>
                                                                                        {{
                                                                                            past.start_date
                                                                                        }}
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th
                                                                                        style="width: 50%"
                                                                                    >
                                                                                        Order
                                                                                        Date:
                                                                                    </th>
                                                                                    <td>
                                                                                        {{
                                                                                            past.order_date
                                                                                        }}
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th
                                                                                        style="width: 50%"
                                                                                    >
                                                                                        Order
                                                                                        Number:
                                                                                    </th>
                                                                                    <td>
                                                                                        {{
                                                                                            past.order_number
                                                                                        }}
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th>
                                                                                        Grand
                                                                                        Total:
                                                                                    </th>
                                                                                    <td>
                                                                                        AED
                                                                                        {{
                                                                                            past.grand_total
                                                                                        }}
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th>
                                                                                        Cutlery?
                                                                                    </th>
                                                                                    <td>
                                                                                        {{
                                                                                            past.cutlery
                                                                                        }}
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th>
                                                                                        Book
                                                                                        Nutritionist?
                                                                                    </th>
                                                                                    <td>
                                                                                        {{
                                                                                            past.book_nutritionist
                                                                                        }}
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else>No past plan found</div>
                                        <!-- /.card -->
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="upcoming_subs">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div
                                            v-if="upcoming_plan_status === true"
                                            class="card card-row card-primary"
                                        >
                                            <div class="card-header bg-success">
                                                <h3 class="card-title">
                                                    {{
                                                        upcoming_plan_details.plan_name
                                                    }}
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div
                                                        class="col-12 col-md-12 col-lg-12 order-2 order-md-1"
                                                    >
                                                        <div class="row">
                                                            <div
                                                                class="col-12 col-sm-3"
                                                            >
                                                                <div
                                                                    class="info-box bg-light"
                                                                >
                                                                    <div
                                                                        class="info-box-content"
                                                                    >
                                                                        <span
                                                                            class="info-box-text text-center text-muted"
                                                                            >Weeks</span
                                                                        >
                                                                        <span
                                                                            class="info-box-number text-center text-muted mb-0"
                                                                            >{{
                                                                                upcoming_plan_details.week
                                                                            }}
                                                                            Week</span
                                                                        >
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div
                                                                class="col-12 col-sm-3"
                                                            >
                                                                <div
                                                                    class="info-box bg-light"
                                                                >
                                                                    <div
                                                                        class="info-box-content"
                                                                    >
                                                                        <span
                                                                            class="
                                                         info-box-text
                                                         text-center text-muted
                                                         "
                                                                            >Days
                                                                            per
                                                                            week</span
                                                                        >
                                                                        <span
                                                                            class="
                                                         info-box-number
                                                         text-center text-muted
                                                         mb-0
                                                         "
                                                                            >{{
                                                                                upcoming_plan_details.day
                                                                            }}
                                                                            Days</span
                                                                        >
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div
                                                                class="col-12 col-sm-3"
                                                            >
                                                                <div
                                                                    class="info-box bg-light"
                                                                >
                                                                    <div
                                                                        class="info-box-content"
                                                                    >
                                                                        <span
                                                                            class="info-box-text text-center text-muted"
                                                                            >Meal
                                                                            per
                                                                            day?</span
                                                                        >
                                                                        <span
                                                                            class="info-box-number text-center text-muted mb-0 "
                                                                            >{{
                                                                                upcoming_plan_details.meal
                                                                            }}
                                                                            Meal</span
                                                                        >
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div
                                                                class="col-12 col-sm-3"
                                                            >
                                                                <div
                                                                    class="info-box bg-light"
                                                                >
                                                                    <div
                                                                        class="info-box-content"
                                                                    >
                                                                        <span
                                                                            class="
                                                         info-box-text
                                                         text-center text-muted
                                                         "
                                                                            >Snack</span
                                                                        >
                                                                        <span
                                                                            class="
                                                         info-box-number
                                                         text-center text-muted
                                                         mb-0
                                                         "
                                                                            >{{
                                                                                upcoming_plan_details.snack
                                                                            }}
                                                                            Snack</span
                                                                        >
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="card card-light card-outline"
                                                >
                                                    <div class="card-header">
                                                        <h5 class="card-title">
                                                            Order Details
                                                        </h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div
                                                                class="col-md-3"
                                                            >
                                                                <img
                                                                    v-bind:src="
                                                                        getPlanTypeImage(
                                                                            upcoming_plan_details.image
                                                                        )
                                                                    "
                                                                    style="object-fit: cover"
                                                                />
                                                            </div>
                                                            <div
                                                                class="col-md-9"
                                                            >
                                                                <div
                                                                    class="table-responsive"
                                                                >
                                                                    <table
                                                                        class="table"
                                                                    >
                                                                        <tbody>
                                                                            <tr>
                                                                                <th
                                                                                    style="width: 50%"
                                                                                >
                                                                                    Meal
                                                                                    Start
                                                                                    Date:
                                                                                </th>
                                                                                <td>
                                                                                    {{
                                                                                        upcoming_plan_details.start_date
                                                                                    }}
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th
                                                                                    style="width: 50%"
                                                                                >
                                                                                    Order
                                                                                    Date:
                                                                                </th>
                                                                                <td>
                                                                                    {{
                                                                                        upcoming_plan_details.order_date
                                                                                    }}
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th
                                                                                    style="width: 50%"
                                                                                >
                                                                                    Order
                                                                                    Number:
                                                                                </th>
                                                                                <td>
                                                                                    {{
                                                                                        upcoming_plan_details.order_number
                                                                                    }}
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th>
                                                                                    Grand
                                                                                    Total:
                                                                                </th>
                                                                                <td>
                                                                                    AED
                                                                                    {{
                                                                                        upcoming_plan_details.grand_total
                                                                                    }}
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th>
                                                                                    Cutlery?
                                                                                </th>
                                                                                <td>
                                                                                    {{
                                                                                        upcoming_plan_details.cutlery
                                                                                    }}
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th>
                                                                                    Book
                                                                                    Nutritionist?
                                                                                </th>
                                                                                <td>
                                                                                    {{
                                                                                        upcoming_plan_details.book_nutritionist
                                                                                    }}
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else>No upcoming plan found</div>
                                        <!-- /.card -->
                                    </div>
                                </div>
                            </div>
                            <!-- /.tab-pane -->
                            <div class="tab-pane" id="timeline">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="card">
                                                        <div
                                                            class="card-header"
                                                        >
                                                            <h3
                                                                class="card-title"
                                                            >
                                                                Meal List
                                                            </h3>
                                                            <div
                                                                class="card-tools"
                                                            >
                                                                <button
                                                                    type="button"
                                                                    class="btn btn-sm btn-primary"
                                                                    @click="
                                                                        newModal
                                                                    "
                                                                >
                                                                    <i
                                                                        class="fa fa-plus-square"
                                                                    ></i>
                                                                    Select Meals
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="card-body table-responsive p-0"
                                                        >
                                                            <div
                                                                v-for="(meals,
                                                                key,
                                                                index) in user_meals"
                                                                :key="index"
                                                            >
                                                                <div
                                                                    class="card-header bg-success"
                                                                >
                                                                    <h3
                                                                        class="card-title"
                                                                    >
                                                                        {{
                                                                            key
                                                                        }}
                                                                    </h3>
                                                                </div>
                                                                <table
                                                                    class="table table-hover"
                                                                >
                                                                    <thead>
                                                                        <tr>
                                                                            <th>
                                                                                Meal
                                                                                ID
                                                                            </th>
                                                                            <th>
                                                                                Meal
                                                                                Name
                                                                            </th>
                                                                            <!-- <th>
                                                                            Date
                                                                        </th> -->
                                                                            <th>
                                                                                Image
                                                                            </th>
                                                                            <th>
                                                                                Quantity
                                                                            </th>
                                                                            <th>
                                                                                Meal/Snack
                                                                            </th>
                                                                            <th>
                                                                                Address
                                                                            </th>
                                                                            <th>
                                                                                Action
                                                                            </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody
                                                                        v-if="
                                                                            meals.length >
                                                                                0
                                                                        "
                                                                    >
                                                                        <tr
                                                                            v-for="meal in meals"
                                                                            :key="
                                                                                meal.id
                                                                            "
                                                                        >
                                                                            <td
                                                                                class="text-capitalize"
                                                                                width="250px"
                                                                            >
                                                                                {{
                                                                                    meal.id
                                                                                }}
                                                                            </td>
                                                                            <td
                                                                                class="text"
                                                                                width="250px"
                                                                            >
                                                                                {{
                                                                                    meal.meal_name
                                                                                }}
                                                                            </td>
                                                                            <td
                                                                                class="text-capitalizetext-capitalize"
                                                                                width="150px"
                                                                            >
                                                                                <img
                                                                                    v-bind:src="
                                                                                        getMealImage(
                                                                                            meal.image
                                                                                        )
                                                                                    "
                                                                                />
                                                                            </td>
                                                                            <td
                                                                                class="text-capitalizetext-capitalize"
                                                                                width="250px"
                                                                            >
                                                                                {{
                                                                                    meal.quantity
                                                                                }}
                                                                            </td>
                                                                            <td
                                                                                class="text-capitalizetext-capitalize"
                                                                                width="250px"
                                                                            >
                                                                                {{
                                                                                    meal.meal_info
                                                                                }}
                                                                            </td>
                                                                            <td
                                                                                class="text-capitalize"
                                                                                width="250px"
                                                                            >
                                                                                {{
                                                                                    meal.address
                                                                                }}
                                                                            </td>
                                                                            <td>
                                                                                <div
                                                                                    class="edit-icon float-right"
                                                                                    style=""
                                                                                >
                                                                                    <a
                                                                                        href="#"
                                                                                    >
                                                                                        <i
                                                                                            class="fa fa-edit nav-icon cyan"
                                                                                            style="float: right;margin-top: 10px;margin-right: 10px;"
                                                                                        ></i
                                                                                    ></a>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                    <tbody
                                                                        v-else
                                                                    >
                                                                        <div
                                                                            class="row"
                                                                        >
                                                                            <div
                                                                                class="col-md-12"
                                                                            >
                                                                                <div
                                                                                    style="text-align: center; padding-top: 15px; padding-bottom: 15px;"
                                                                                >
                                                                                    No
                                                                                    Meals
                                                                                    Found
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="card-footer"
                                                        >
                                                            <!---->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="myMeals">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-row card-primary">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="card">
                                                            <div
                                                                class="card-body table-responsive p-0"
                                                            >
                                                                <table
                                                                    class="table table-hover"
                                                                >
                                                                    <thead>
                                                                        <tr>
                                                                            <th>
                                                                                Meal
                                                                                Name
                                                                            </th>
                                                                            <th>
                                                                                Date
                                                                            </th>
                                                                            <th>
                                                                                Image
                                                                            </th>
                                                                            <th>
                                                                                Quantity
                                                                            </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr
                                                                            v-for="meals in user_meals"
                                                                            :key="
                                                                                meals.meal_data
                                                                            "
                                                                        >
                                                                            <td
                                                                                class="text-capitalize"
                                                                                width="250px"
                                                                            >
                                                                                {{
                                                                                    meals.meal_name
                                                                                }}
                                                                            </td>
                                                                            <td
                                                                                class="text-capitalizetext-capitalize"
                                                                                width="250px"
                                                                            >
                                                                                {{
                                                                                    meals.date
                                                                                }}
                                                                            </td>
                                                                            <td
                                                                                class="text-capitalizetext-capitalize"
                                                                                width="250px"
                                                                            >
                                                                                <img
                                                                                    v-bind:src="
                                                                                        getMealImage(
                                                                                            meals.image
                                                                                        )
                                                                                    "
                                                                                    :style="{
                                                                                        width:
                                                                                            '80px',
                                                                                        height:
                                                                                            '80px'
                                                                                    }"
                                                                                />
                                                                            </td>
                                                                            <td
                                                                                class="text-capitalizetext-capitalize"
                                                                                width="250px"
                                                                            >
                                                                                {{
                                                                                    meals.quantity
                                                                                }}
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div
                                                                class="card-footer"
                                                            >
                                                                <!---->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="feedback">
                                <div class="row">
                                    <div class="card-body p-0">
                                        <div
                                            class="table-responsive mailbox-messages"
                                        >
                                            <table class="table table-hover">
                                                <thead>
                                                    <th>Message</th>
                                                    <th>Date</th>
                                                    <th>Action</th>
                                                </thead>
                                                <tbody>
                                                    <tr
                                                        v-for="feed in feedbacks.data"
                                                        :key="feed.id"
                                                    >
                                                        <td id="overflowTest">
                                                            {{ feed.message }}
                                                        </td>
                                                        <td
                                                            class="mailbox-date"
                                                        >
                                                            {{ feed.date }}
                                                        </td>
                                                        <td>
                                                            <a
                                                                class="btn btn-success"
                                                                v-bind:href="
                                                                    '/feeds/' +
                                                                        feed.id +
                                                                        '/view'
                                                                "
                                                                >View Details</a
                                                            >
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="card-footer">
                                            <pagination
                                                :data="feedbacks"
                                                @pagination-change-page="
                                                    getFeedResults
                                                "
                                            ></pagination>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="bug">
                                <div class="card-body p-0">
                                    <div
                                        class="table-responsive mailbox-messages"
                                    >
                                        <table class="table table-hover">
                                            <tbody>
                                                <tr
                                                    v-for="bug in bugs.data"
                                                    :key="bug.id"
                                                >
                                                    <td class="mailbox-name">
                                                        <a
                                                            v-bind:href="
                                                                '/bugs/' +
                                                                    bug.id +
                                                                    '/view'
                                                            "
                                                        >
                                                            {{ bug.name }}</a
                                                        >
                                                    </td>
                                                    <td class="mailbox-subject">
                                                        {{ bug.message }}
                                                    </td>
                                                    <td class="mailbox-date">
                                                        {{ bug.date }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-footer">
                                        <pagination
                                            :data="bugs"
                                            @pagination-change-page="
                                                getBugsResults
                                            "
                                        ></pagination>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="notification">
                                <div class="card-body p-0">
                                    <div
                                        class="table-responsive mailbox-messages"
                                    >
                                        <table class="table table-hover">
                                            <thead>
                                                <th>Title</th>
                                                <th>Message</th>
                                                <th>Date</th>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="notification in notifications.data"
                                                    :key="notification.id"
                                                >
                                                    <td>
                                                        {{ notification.title }}
                                                    </td>
                                                    <td class="mailbox-subject">
                                                        {{
                                                            notification.message
                                                        }}
                                                    </td>
                                                    <td class="mailbox-date">
                                                        {{
                                                            notification.created_at
                                                        }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-footer">
                                        <pagination
                                            :data="bugs"
                                            @pagination-change-page="
                                                getNotificationResults
                                            "
                                        ></pagination>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="transaction">
                                <div class="card-body p-0">
                                    <div
                                        class="table-responsive mailbox-messages"
                                    >
                                        <table class="table table-hover">
                                            <thead>
                                                <th>Transaction Id</th>
                                                <th>Plan Name</th>
                                                <th>Price</th>
                                                <th>Payment Type</th>
                                                <th>Transaction Date</th>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="transaction in transactions.data"
                                                    :key="transaction.id"
                                                >
                                                    <td>
                                                        {{
                                                            transaction.order_number
                                                        }}
                                                    </td>
                                                    <td>
                                                        {{
                                                            transaction.plan_name
                                                        }}
                                                    </td>
                                                    <td>
                                                        {{ transaction.price }}
                                                    </td>
                                                    <td>
                                                        {{
                                                            transaction.payment_mode
                                                        }}
                                                    </td>
                                                    <td>
                                                        {{
                                                            transaction.created_at
                                                        }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-footer">
                                        <pagination
                                            :data="transactions"
                                            @pagination-change-page="
                                                getTransactionsResults
                                            "
                                        ></pagination>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="notes">
                                <div class="card-body p-0">
                                    <textarea
                                        id="w3review"
                                        name="w3review"
                                        v-model="notes"
                                        rows="4"
                                        cols="50"
                                    ></textarea>
                                    <br />
                                    <button
                                        type="submit"
                                        value="Submit"
                                        class="btn btn-success"
                                        @click="addNotes()"
                                    >
                                        Submit
                                    </button>
                                </div>
                            </div>
                            <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
        <!-- Add Modal -->
        <div
            class="modal fade"
            id="addNew"
            tabindex="-1"
            role="dialog"
            aria-labelledby="addNew"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content" style="width: 110% !important">
                    <div class="modal-header">
                        <h5 class="modal-title">Select Meal Items</h5>
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="typo__label">Date</label>
                                <datetime
                                    format="YYYY-MM-DD"
                                    width="300px"
                                    id="date-picker"
                                ></datetime>
                                <button
                                    class="btn btn-primary"
                                    @click="findMeals()"
                                >
                                    Find Meals
                                </button>
                            </div>
                        </div>
                        <div class="card-body" id="select-meals">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                Meal List
                                            </h3>
                                        </div>
                                        <div
                                            class="card-body table-responsive p-0"
                                        >
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Image</th>
                                                        <th>Category</th>
                                                        <th>Meal/Snack</th>
                                                        <th>Choose Address</th>
                                                        <th>Quantity</th>
                                                    </tr>
                                                </thead>
                                                <tbody
                                                    id="selcted-meals"
                                                    v-if="meal_data.length > 0"
                                                >
                                                    <tr
                                                        v-for="meal in meal_data"
                                                        :key="meal.id"
                                                    >
                                                        <td
                                                            class="text-capitalize"
                                                            width="250px"
                                                        >
                                                            {{ meal.name }}
                                                        </td>
                                                        <td>
                                                            <img
                                                                v-bind:src="
                                                                    getMealImage(
                                                                        meal.image
                                                                    )
                                                                "
                                                            />
                                                        </td>
                                                        <td>
                                                            {{ meal.category }}
                                                        </td>
                                                        <td>
                                                            {{ meal.meal_info }}
                                                        </td>
                                                        <td>
                                                            <select
                                                                name="user_address"
                                                                class="form-control"
                                                            >
                                                                <option value=""
                                                                    >Select
                                                                    Address</option
                                                                >
                                                                <option
                                                                    v-for="address in addresses"
                                                                    :value="
                                                                        address.id
                                                                    "
                                                                >
                                                                    <input
                                                                        type="text"
                                                                        :value="
                                                                            address.id
                                                                        "
                                                                        :id="
                                                                            'user_address'
                                                                        "
                                                                    />
                                                                    {{
                                                                        address.current_location
                                                                    }}
                                                                </option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <button
                                                                v-on:click="
                                                                    Down($event)
                                                                "
                                                                :id="
                                                                    'down_' +
                                                                        meal.id
                                                                "
                                                            >
                                                                -
                                                            </button>
                                                            <input
                                                                type="text"
                                                                :value="
                                                                    meal.quantity
                                                                "
                                                                :id="
                                                                    'input_' +
                                                                        meal.id
                                                                "
                                                            />
                                                            <input
                                                                type="hidden"
                                                                :value="
                                                                    meal.is_meal
                                                                "
                                                                :id="
                                                                    'ismeal_' +
                                                                        meal.id
                                                                "
                                                            />
                                                            <input
                                                                type="hidden"
                                                                :value="
                                                                    available_meals
                                                                "
                                                                :id="
                                                                    'available_meals'
                                                                "
                                                            />
                                                            <input
                                                                type="hidden"
                                                                :value="
                                                                    available_sancks
                                                                "
                                                                :id="
                                                                    'available_sancks'
                                                                "
                                                            />
                                                            <button
                                                                :id="
                                                                    'up_' +
                                                                        meal.id
                                                                "
                                                                v-on:click="
                                                                    Up($event)
                                                                "
                                                            >
                                                                +
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tbody v-else>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div
                                                                style="text-align: center; padding-top: 15px; padding-bottom: 15px;"
                                                            >
                                                                No Meals Found
                                                            </div>
                                                        </div>
                                                    </div>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="card-footer"></div>
                                    </div>
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
                            <button
                                type="submit"
                                class="btn btn-primary"
                                @click="saveMeals()"
                            >
                                Save Meals
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
                        <h5 class="modal-title">Update Meal</h5>
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="updateMeal">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Meal</label>
                                <select
                                    name="edit_meal"
                                    class="form-control"
                                    v-model="form.edit_meal"
                                    :class="{
                                        'is-invalid': form.errors.has(
                                            'edit_meal'
                                        )
                                    }"
                                >
                                    <option value="">Select Meal</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                                <has-error
                                    :form="form"
                                    field="edit_meal"
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

        <!-- edit Modal -->
        <div
            class="modal fade"
            id="editNewSnack"
            tabindex="-1"
            role="dialog"
            aria-labelledby="editNew"
            aria-hidden="true"
        >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Snack</h5>
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="updateSnack">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Snack</label>
                                <select
                                    name="edit_snack"
                                    class="form-control"
                                    v-model="form.edit_snack"
                                    :class="{
                                        'is-invalid': form.errors.has(
                                            'edit_snack'
                                        )
                                    }"
                                >
                                    <option value="">Select Snack</option>
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                </select>
                                <has-error
                                    :form="form"
                                    field="edit_snack"
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

        <!-- user edit Modal -->
        <div
            class="modal fade"
            id="editNewUser"
            tabindex="-1"
            role="dialog"
            aria-labelledby="editNew"
            aria-hidden="true"
        >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update User</h5>
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form @submit.prevent="updateAdminUser">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Mobile</label>
                                <input
                                    v-model="form.mobile_number"
                                    type="text"
                                    name="mobile_number"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.errors.has(
                                            'mobile_number'
                                        )
                                    }"
                                    placeholder="Enter mobile_number"
                                />
                                <has-error
                                    :form="form"
                                    field="mobile_number"
                                ></has-error>
                            </div>

                            <div class="form-group">
                                <label>Gender</label>
                                <input
                                    v-model="form.gender"
                                    type="text"
                                    name="gender"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.errors.has('gender')
                                    }"
                                    placeholder="Enter Gender"
                                />
                                <has-error
                                    :form="form"
                                    field="gender"
                                ></has-error>
                            </div>

                            <div class="form-group">
                                <label>DOB</label>
                                <input
                                    v-model="form.dob"
                                    type="text"
                                    name="dob"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.errors.has('dob')
                                    }"
                                    placeholder="Enter DOB"
                                />
                                <has-error :form="form" field="dob"></has-error>
                            </div>

                            <div class="form-group">
                                <label>Height</label>
                                <input
                                    v-model="form.height"
                                    type="text"
                                    name="height"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.errors.has('height')
                                    }"
                                    placeholder="Enter Height"
                                />
                                <has-error
                                    :form="form"
                                    field="height"
                                ></has-error>
                            </div>

                            <div class="form-group">
                                <label>Weight</label>
                                <input
                                    v-model="form.weight"
                                    type="text"
                                    name="weight"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.errors.has('weight')
                                    }"
                                    placeholder="Enter Weight"
                                />
                                <has-error
                                    :form="form"
                                    field="weight"
                                ></has-error>
                            </div>

                            <div class="form-group">
                                <label>Blood Group</label>
                                <input
                                    v-model="form.blood_group"
                                    type="text"
                                    name="blood_group"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.errors.has(
                                            'blood_group'
                                        )
                                    }"
                                    placeholder="Enter Blood Group"
                                />
                                <has-error
                                    :form="form"
                                    field="blood_group"
                                ></has-error>
                            </div>

                            <div class="form-group">
                                <label>Allergies</label>
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
                                <has-error
                                    :form="form"
                                    field="allergies"
                                ></has-error>
                            </div>

                            <div class="form-group">
                                <label>Cardio</label>
                                <input
                                    v-model="form.cardio"
                                    type="text"
                                    name="cardio"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.errors.has('cardio')
                                    }"
                                    placeholder="Enter Cardio"
                                />
                                <has-error
                                    :form="form"
                                    field="cardio"
                                ></has-error>
                            </div>

                            <div class="form-group">
                                <label>Medical Conditions</label>
                                <input
                                    v-model="form.med_conditions"
                                    type="text"
                                    name="med_conditions"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.errors.has(
                                            'med_conditions'
                                        )
                                    }"
                                    placeholder="Enter Medical Conditions"
                                />
                                <has-error
                                    :form="form"
                                    field="med_conditions"
                                ></has-error>
                            </div>

                            <div class="form-group">
                                <label>Book Nutritionist</label>
                                <select
                                    name="book_nutritionist"
                                    class="form-control"
                                    v-model="form.book_nutritionist"
                                    :class="{
                                        'is-invalid': form.errors.has(
                                            'book_nutritionist'
                                        )
                                    }"
                                >
                                    <option value="">Select Option</option>
                                    <option value="true">Yes</option>
                                    <option value="false">No</option>
                                </select>

                                <has-error
                                    :form="form"
                                    field="book_nutritionist"
                                ></has-error>
                            </div>

                            <div class="form-group">
                                <label>Cutlery</label>
                                <select
                                    name="cutlery"
                                    class="form-control"
                                    v-model="form.cutlery"
                                    :class="{
                                        'is-invalid': form.errors.has('cutlery')
                                    }"
                                >
                                    <option value="">Select Option</option>
                                    <option value="true">Yes</option>
                                    <option value="false">No</option>
                                </select>
                                <has-error
                                    :form="form"
                                    field="cutlery"
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
        <!-- user edit Modal -->
    </div>
</template>
<script>
import datetime from "vuejs-datetimepicker";
import Multiselect from "vue-multiselect";

export default {
    components: {
        Multiselect,
        datetime
    },
    data() {
        return {
            form: new Form({
                edit_meal: "",
                edit_snack: "",
                mobile_number: "",
                gender: "",
                dob: "",
                height: "",
                weight: "",
                blood_group: "",
                allergies: "",
                cardio: "",
                med_conditions: "",
                book_nutritionist: "",
                cutlery: ""
            }),
            image: `/images/profile/user8-128x128.jpg`,
            image1: `/images/profile/user8-128x128.jpg`,
            image2: `/images/profile/user7-128x128.jpg`,
            image3: `/images/profile/user6-128x128.jpg`,
            image4: `/images/profile/photo1.png`,
            image5: `/images/profile/photo2.png`,
            image6: `/images/profile/photo3.jpg`,
            image7: `/images/profile/photo4.jpg`,
            getPlanTypeImage(img) {
                return `/images/api_images/${img}`;
            },
            getUserImage(img) {
                return `/images/users/${img}`;
            },
            plan_status: false,
            past_plan_status: false,
            upcoming_plan_status: false,
            toggle_book: "",
            toggle_cutlery: "",
            options: [],
            addresses: {},
            value: [],
            profile: {},
            completed: {},
            meal_data: {},
            plan_details: {},
            past_plan_details: [],
            upcoming_plan_details: {},
            user_meals: {},
            user_address: {},
            available_meals: {},
            available_sancks: {},
            dates: [],
            weekdays: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
            feedbacks: {},
            bugs: {},
            transactions: {},
            notes: "",
            notifications: {}
        };
    },
    methods: {
        loadActivePlan() {
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/myActiveMeal/" + this.$route.params.id)
                    .then(({ data }) => {
                        this.plan_status = data.status;
                        this.plan_details = data.data;
                        // this.editModelMeal(data.data);
                        // this.editModelSnack(data.data);
                    });
            }
        },
        loadPastPlan() {
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/myPastPlan/" + this.$route.params.id)
                    .then(({ data }) => {
                        this.past_plan_status = data.status;
                        this.past_plan_details = data.data;
                        console.log(this.past_plan_details);
                        // this.editModelMeal(data.data);
                        // this.editModelSnack(data.data);
                    });
            }
        },
        loadUpcomingPlan() {
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/myUpcomingPlan/" + this.$route.params.id)
                    .then(({ data }) => {
                        this.upcoming_plan_status = data.status;
                        this.upcoming_plan_details = data.data;
                        console.log(this.upcoming_plan_details);
                        // this.editModelMeal(data.data);
                        // this.editModelSnack(data.data);
                    });
            }
        },

        samplefunction() {
            Swal.fire({
                title: "Are you sure?",
                text: "Is Book Nutritionist Done?",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, done!"
            }).then(result => {
                axios
                    .post(
                        "/api/bookNutritionistChecked/" + this.$route.params.id
                    )
                    .then(response => {
                        window.location.reload();
                    })
                    .catch(error => {
                        console.log(error);
                    });
            });
        },

        samplefunction1(event) {
            console.log(event);
        },

        loadAddress() {
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/loadAddress/" + this.$route.params.id)
                    .then(({ data }) => (this.addresses = data.data));
            }
        },
        addNotes() {
            console.log(this.notes);
            if (this.$gate.isAdmin()) {
                axios
                    .post(
                        "/api/addNotes/" +
                            this.$route.params.id +
                            "/" +
                            this.notes
                    )
                    .then(({ data }) =>
                        Toast.fire({
                            icon: "success",
                            title: data.message
                        })
                    )
                    .catch(() => {
                        Toast.fire({
                            icon: "error",
                            title: "Please enter some notes before submitting!!"
                        });
                    });
            }
        },
        loadNotes() {
            console.log(this.notes);
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/getNotes/" + this.$route.params.id)
                    .then(({ data }) => (this.notes = data.data));
            }
        },

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

        getSelectedOptions() {
            var final_options = [];
            this.value.forEach(e => {
                final_options.push(e);
            });
            this.form.allergies = final_options;
        },

        loadSelectedAllergens(allergens) {
            console.log(allergens.split(","));
            this.value = [];
            if (allergens.length > 0) {
                allergens.split(",").forEach(e => {
                    var object = {};
                    object = { name: e };
                    this.value.push(object);
                });
            }
        },

        editModelMeal(plan_details) {
            this.editmode = true;
            this.form.reset();
            this.form.edit_meal = plan_details;
            console.log(plan_details);
            $("#editNew").modal("show");
        },

        editModelUser(profile) {
            this.editmode = true;
            this.form.reset();
            this.form.fill(profile);
            this.loadSelectedAllergens(profile.allergies);
            console.log(profile);
            $("#editNewUser").modal("show");
        },

        updateAdminUser(profile) {
            this.$Progress.start();
            this.getSelectedOptions();
            this.form
                .post("/api/updateAdminUser/" + this.$route.params.id)
                .then(response => {
                    // success
                    console.log(response);
                    if (response.data.status == false) {
                        Toast.fire({
                            icon: "error",
                            title: response.data.message
                        });
                    } else {
                        $("#editNewUser").modal("hide");
                        Toast.fire({
                            icon: "success",
                            title: response.data.message
                        });
                        this.$router.push(
                            "/freshly_users/" + this.$route.params.id + "/view"
                        );
                        window.location.reload();
                        this.$Progress.finish();
                    }
                })
                .catch(response => {
                    console.log(response);
                    Toast.fire({
                        icon: "error",
                        title: response.data.message
                    });
                });
        },

        updateMeal(plan_details) {
            this.$Progress.start();
            this.form
                .put("/api/updateMeal/" + this.$route.params.id)
                .then(response => {
                    // success
                    console.log(response);
                    if (response.data.status == false) {
                        Toast.fire({
                            icon: "error",
                            title: response.data.message
                        });
                    } else {
                        $("#editNew").modal("hide");
                        Toast.fire({
                            icon: "success",
                            title: response.data.message
                        });
                        this.$router.push("/freshly_users");
                        this.$Progress.finish();
                    }
                })
                .catch(response => {
                    console.log(response);
                    Toast.fire({
                        icon: "error",
                        title: response.data.message
                    });
                });
        },

        editModelSnack(plan_details) {
            this.editmode = true;
            this.form.reset();
            this.form.edit_snack = plan_details;
            console.log(plan_details);
            $("#editNewSnack").modal("show");
        },

        updateSnack(plan_details) {
            this.$Progress.start();
            this.form
                .put("/api/updateSnack/" + this.$route.params.id)
                .then(response => {
                    if (response.data.status == false) {
                        Toast.fire({
                            icon: "error",
                            title: response.data.message
                        });
                    } else {
                        $("#editNewSnack").modal("hide");
                        Toast.fire({
                            icon: "success",
                            title: response.data.message
                        });
                        this.$router.push("/freshly_users");
                        this.$Progress.finish();
                    }
                })
                .catch(response => {
                    Toast.fire({
                        icon: "error",
                        title: response.data.message
                    });
                });
        },
        getFeedResults(page = 1) {
            this.$Progress.start();
            axios
                .get(
                    "/api/feedbackByUserId/" +
                        this.$route.params.id +
                        "?page=" +
                        page
                )
                .then(({ data }) => (this.feedbacks = data.data));

            this.$Progress.finish();
        },

        getBugsResults(page = 1) {
            this.$Progress.start();
            axios
                .get(
                    "/api/bugByUserId/" +
                        this.$route.params.id +
                        "?page=" +
                        page
                )
                .then(({ data }) => (this.bugs = data.data));

            this.$Progress.finish();
        },

        getTransactionsResults(page = 1) {
            this.$Progress.start();
            axios
                .get(
                    "/api/transactionByUserId/" +
                        this.$route.params.id +
                        "?page=" +
                        page
                )
                .then(({ data }) => (this.transactions = data.data));

            this.$Progress.finish();
        },

        getNotificationResults(page = 1) {
            this.$Progress.start();
            axios
                .get(
                    "/api/notificationByUserId/" +
                        this.$route.params.id +
                        "?page=" +
                        page
                )
                .then(({ data }) => (this.notifications = data.data));

            this.$Progress.finish();
        },

        getImagePath(img) {
            return `/images/profile/${img}`;
        },

        Up(event) {
            let currentCount = event.target.id;
            let splitUpCount = currentCount.split("_");
            let getId = splitUpCount[1];
            let inputValue = document.getElementById("input_" + getId).value;
            let is_meal = document.getElementById("ismeal_" + getId).value;
            let snacks = document.getElementById("available_sancks").value;
            let meals = document.getElementById("available_meals").value;
            let inputValueInc = parseInt(inputValue) + 1;
            if (is_meal == "1") {
                if (inputValueInc > meals) {
                    alert("Meals cannot be greater than " + meals);
                    inputValueInc = parseInt(inputValue);
                }
            } else {
                if (inputValueInc > snacks) {
                    alert("Snacks cannot be greater than " + snacks);
                    inputValueInc = parseInt(inputValue);
                }
            }
            let finalInputVal = (document.getElementById(
                "input_" + getId
            ).value = inputValueInc);
        },
        Down(event) {
            let currentCount = event.target.id;
            let splitUpCount = currentCount.split("_");
            let getId = splitUpCount[1];
            let inputValue = document.getElementById("input_" + getId).value;
            let is_meal = document.getElementById("ismeal_" + getId).value;
            let snacks = document.getElementById("available_sancks").value;
            let meals = document.getElementById("available_meals").value;
            let inputValueInc = parseInt(inputValue) - 1;
            if (is_meal == "1") {
                if (inputValueInc < 0) {
                    alert("Meals cannot be greater 0");
                    inputValueInc = parseInt(inputValue);
                }
            } else {
                if (inputValueInc < 0) {
                    alert("Snacks cannot be less than 0");
                    inputValueInc = parseInt(inputValue);
                }
            }

            let finalInputVal = (document.getElementById(
                "input_" + getId
            ).value = inputValueInc);
            console.log(finalInputVal);
        },

        getMealImage(img) {
            return `/images/api_images/${img}`;
        },

        newModal() {
            $("#addNew").modal("show");
        },

        chnageMeal(event) {
            var date = event.currentTarget.getAttribute("data-date");
            this.mealChnage(date);
        },

        loadProfile() {
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/freshlyUserProfile/" + this.$route.params.id)
                    .then(({ data }) => {
                        this.profile = data.data;
                    });
            }
        },

        setData(date, userId) {
            this.completed = {};
            this.completed.user_id = userId;
            this.completed.date = date;
            this.category = "";
        },

        renderMeal(meals) {
            console.log(meals);
            this.meal_data = {};
            this.meal_data = meals.meal_data;
        },

        findMeals() {
            var datetime = $("#tj-datetime-input")
                .val()
                .toString();
            console.log(datetime);
            const meal_date = {
                date: datetime,
                user_id: this.$route.params.id
            };
            if (this.$gate.isAdmin()) {
                axios
                    .post("/api/MealListDate", meal_date)
                    .then(response => {
                        this.renderMeal(response.data.data[0]),
                            (this.available_meals =
                                response.data.data[0].available_meals);
                        this.available_sancks =
                            response.data.data[0].available_sancks;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            }
        },

        backButton() {
            history.back();
        },

        saveMeals() {
            let selected = document.getElementById("selcted-meals");
            // let meals = document.getElementById("input_130").value;
            let user_address = document.getElementById("user_address").value;
            let children = selected.children;
            console.log(user_address);
            const datetime = $("#tj-datetime-input")
                .val()
                .toString();
            let array_value = [];
            for (let i = 0; i < children.length; i++) {
                const element =
                    children[i].lastElementChild.childNodes[2].value;
                const elementKey =
                    children[i].lastElementChild.childNodes[2].id;
                const mealIdSplit = elementKey.split("_");
                const mealId = mealIdSplit[1];
                // array_value.push({id: element.})
                console.log(element);
                console.log(mealId);
                console.log(datetime);
                array_value.push({
                    id: mealId,
                    quantity: element,
                    user_id: this.$route.params.id,
                    date: datetime
                });
            }
            if (this.$gate.isAdmin()) {
                axios
                    .post("/api/saveMealAdmin", array_value)
                    .then(response => {
                        console.log(response);
                        Toast.fire({
                            icon: "success",
                            title: response.message
                        });
                        location.reload();
                        this.$Progress.finish();
                    })
                    .catch(error => {
                        console.log(error);
                        Toast.fire({
                            icon: "error",
                            title: "Some error occured! Please try again"
                        });
                    });
            }
        },

        addDays(date, days) {
            const d = new Date(date);
            d.setDate(d.getDate() + days);
            let finalDate = this.formatDate(d);
            return finalDate;
        },

        formatDate(date) {
            return date.toISOString().split("T")[0];
        },

        loadDates() {
            let finalDate;
            const currentDate = this.formatDate(new Date());
            const createdDate = this.addDays(currentDate, 3);
            for (let date = 0; date < 6; date++) {
                finalDate = date > 0 ? this.addDays(finalDate, 1) : createdDate;
                const month = this.getMonthShort(finalDate);
                const day = this.findWeekDay(finalDate);
                const dateOnly = this.getDate(finalDate);
                this.dates.push({
                    count: date,
                    fullDate: finalDate,
                    month: month,
                    weekday: day,
                    date: dateOnly
                });
            }
        },

        getMonthShort(date) {
            const today = new Date(date);
            return today.toLocaleString("default", { month: "short" });
        },

        findWeekDay(date) {
            var date = new Date(date);
            var n = date.getDay();
            return this.weekdays[n];
        },

        getDate(date) {
            const dateOnly = date.split("-");
            return dateOnly[2];
        },
        loadUserMeals() {
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/freshlyUsersMeals/" + this.$route.params.id)
                    .then(({ data }) => {
                        this.user_meals = data.data;
                    });
            }
        },
        loadUserAddress() {
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/getAddressForAdmin/" + this.$route.params.id)
                    .then(({ data }) => {
                        this.user_address = data.data;
                    });
            }
        },

        loadFeedbackList() {
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/feedbackByUserId/" + this.$route.params.id)
                    .then(({ data }) => (this.feedbacks = data.data));
            }
        },

        loadTransactionsList() {
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/transactionByUserId/" + this.$route.params.id)
                    .then(({ data }) => (this.transactions = data.data));
            }
        },

        loadNotificationsList() {
            if (this.$gate.isAdmin()) {
                axios
                    .get("/api/notificationByUserId/" + this.$route.params.id)
                    .then(({ data }) => (this.notifications = data.data));
            }
        }
    },
    created() {
        this.$Progress.start();
        this.loadProfile();
        this.loadAddress();
        this.loadNotes();
        this.loadAllergens();
        this.loadActivePlan();
        this.loadPastPlan();
        this.loadUpcomingPlan();
        this.loadUserMeals();
        this.loadUserAddress();
        this.loadFeedbackList();
        this.loadTransactionsList();
        this.loadNotificationsList();
        // this.loadDates();
        // this.loadMealsByDate();
        this.$Progress.finish();
    }
};
</script>
<style></style>
