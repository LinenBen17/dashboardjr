/* $(document).ready(function () {
    console.log("Employee JS loaded");
    $('#data\\.birth_date').blur(function () {
        $('#data\\.age').val(calculateAge($(this).val()));
    })
    $('#commets').change(function () {
        console.log("A")
        $('#data\\.age').val(calculateAge($('#data\\.birth_date').val()));
    })

    $('#data\\.civil_status_id').blur(function () {
        $('#data\\.address').focus();
    })
})

function calculateAge(birthDate) {
    const birth = new Date(birthDate);
    const today = new Date();
    let age = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
        age--;
    }
    return age;
} */