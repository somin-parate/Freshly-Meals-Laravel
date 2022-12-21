export default class Custom{
    constructor(){
        // alert('custom js');
        $(function () {
            $('#reservationdatetime').datepicker({ icons: { time: 'far fa-clock' } });
        });
    }
}

