<template>
    <datetime format="h:i:s" width="300px" v-model="val"></datetime>
</template>
 
<script>
import datetime from 'vuejs-datetimepicker';
 
export default {
    components: { datetime }
};
</script> 