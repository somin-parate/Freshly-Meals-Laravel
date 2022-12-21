export default [
    {
        name: "dashboard",
        path: "/dashboard",
        component: require("./components/Dashboard.vue").default
    },
    {
        name: "profile",
        path: "/profile",
        component: require("./components/Profile.vue").default
    },
    {
        name: "users",
        path: "/users",
        component: require("./components/Users.vue").default
    },
    {
        name: "freshly_users",
        path: "/freshly_users",
        component: require("./components/freshlyUsers.vue").default
    },
    {
        name: "viewProfile",
        path: "/freshly_users/:id/view",
        component: require("./components/viewProfile.vue").default
    },
    {
        name: "plans",
        path: "/plans",
        component: require("./components/Plan.vue").default
    },
    {
        name: "newPlan",
        path: "/plans/create",
        component: require("./components/addPlan.vue").default
    },
    {
        name: "planedit",
        path: "/plan/:id/edit",
        component: require("./components/editPlan.vue").default
    },
    {
        name: "foodItems",
        path: "/foodItems",
        component: require("./components/foodItems.vue").default
    },
    {
        name: "meals",
        path: "/meals",
        component: require("./components/Meal.vue").default
    },
    {
        name: "newMeal",
        path: "/meals/create",
        component: require("./components/addMeal.vue").default
    },
    {
        name: "mealedit",
        path: "/meal/:id/edit",
        component: require("./components/editMeal.vue").default
    },
    {
        name: "offers",
        path: "/offers",
        component: require("./components/Offer.vue").default
    },
    {
        path: "*",
        component: require("./components/NotFound.vue").default
    },
    {
        name: "allergens",
        path: "/allergens",
        component: require("./components/Allergens.vue").default
    },
    {
        name: "cities",
        path: "/cities",
        component: require("./components/City.vue").default
    },
    {
        name: "timeslots",
        path: "/timeslots",
        component: require("./components/Timeslot.vue").default
    },
    {
        name: "todayMeals",
        path: "/todayMeals",
        component: require("./components/todayMeals.vue").default
    },
    {
        name: "todayParcel",
        path: "/todayParcel",
        component: require("./components/todayParcels.vue").default
    },
    {
        name: "multi-image",
        path: "/multi-image",
        component: require("./components/MultipleImageUploadComponent.vue")
            .default
    },
    {
        name: "bankRequests",
        path: "/bankRequests",
        component: require("./components/bankPayment.vue").default
    },
    {
        name: "onlinePayments",
        path: "/onlinePayments",
        component: require("./components/bankPayment.vue").default
    },
    {
        name: "feedback",
        path: "/feedback",
        component: require("./components/feedback.vue").default
    },
    {
        name: "faqs",
        path: "/faqs",
        component: require("./components/faqs.vue").default
    },
    {
        name: "help_support",
        path: "/help_support",
        component: require("./components/faqs.vue").default
    },
    {
        name: "general_issues",
        path: "/general_issues",
        component: require("./components/faqs.vue").default
    },
    {
        name: "viewFeedbacks",
        path: "/feeds/:id/view",
        component: require("./components/viewFeedbacks.vue").default
    },
    {
        name: "viewBugs",
        path: "/bugs/:id/view",
        component: require("./components/viewBugs.vue").default
    },
    {
        name: "viewBagRefunds",
        path: "/refunds/:id/view",
        component: require("./components/viewBagRefunds.vue").default
    },
    {
        name: "notifications",
        path: "/notifications",
        component: require("./components/notification.vue").default
    },
    {
        name: "banners",
        path: "/banners",
        component: require("./components/bannerImages.vue").default
    },
    {
        name: "todayDelivery",
        path: "/todayDelivery",
        component: require("./components/todayDeliverys.vue").default
    }
    // {
    //     name: 'multiselect',
    //     path: '/multiselect',
    //     component: require('./components/Multiselect.vue').default
    // },
    // {
    //     name: 'datetimepicker',
    //     path: '/datetimepicker',
    //     component: require('./components/DatetimePicker.vue').default
    // },
];
