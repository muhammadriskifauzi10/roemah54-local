$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $(window).on("scroll", function () {
        let valueWindowScroll = window.scrollY;
        if (valueWindowScroll > 34) {
            $("#nav-info").hide();
        } else {
            $("#nav-info").show();
        }

        // Mendapatkan tinggi elemen dan tinggi jendela browser
        var documentHeight = Math.max(
            document.body.scrollHeight,
            document.body.offsetHeight,
            document.documentElement.clientHeight,
            document.documentElement.scrollHeight,
            document.documentElement.offsetHeight
        );
        var windowHeight =
            window.innerHeight || document.documentElement.clientHeight;

        // Mendapatkan posisi scroll vertikal
        var scrollPosition =
            window.scrollY ||
            window.pageYOffset ||
            document.documentElement.scrollTop;

        // Menghitung bagian paling bawah dari halaman
        var bottomOfPage = documentHeight - windowHeight;

        // Memeriksa apakah posisi scroll saat ini berada di bagian paling bawah
        if (scrollPosition >= bottomOfPage - 145) {
            $(".wa-link").addClass('hideWaLinkAnimation');
        } else {
            $(".wa-link").removeClass('hideWaLinkAnimation');
        }
    });

    $("#btn-request").on("click", function () {
        $(this).text("Sebentar...");
    });
});

function pilihProvinsi() {
    let province_id = $("#province_id").val();

    $.ajax({
        url: "/pilihprovinsi",
        type: "POST",
        data: {
            province_id: province_id,
        },
        success: function (response) {
            if (response.message == "success") {
                $("#regency_id").empty();
                $("#regency_id").append(
                    '<option value="0">Pilih Kabupaten</option>'
                );

                $("#district_id").empty();
                $("#district_id").append(
                    '<option value="0">Pilih Kecamatan</option>'
                );

                $("#village_id").empty();
                $("#village_id").append(
                    '<option value="0">Pilih Kelurahan</option>'
                );

                $("#regency_id").append(response.dataHTML.trim());
            }
        },
    });
}

function pilihKabupaten() {
    let regency_id = $("#regency_id").val();

    $.ajax({
        url: "/pilihkabupaten",
        type: "POST",
        data: {
            regency_id: regency_id,
        },
        success: function (response) {
            if (response.message == "success") {
                $("#district_id").empty();
                $("#district_id").append(
                    '<option value="0">Pilih Kecamatan</option>'
                );

                $("#village_id").empty();
                $("#village_id").append(
                    '<option value="0">Pilih Kelurahan</option>'
                );

                $("#district_id").append(response.dataHTML.trim());
            }
        },
    });
}

function pilihKecamatan() {
    let district_id = $("#district_id").val();

    $.ajax({
        url: "/pilihkecamatan",
        type: "POST",
        data: {
            district_id: district_id,
        },
        success: function (response) {
            if (response.message == "success") {
                $("#village_id").empty();
                $("#village_id").append(
                    '<option value="0">Pilih Kelurahan</option>'
                );
                $("#village_id").append(response.dataHTML.trim());
            }
        },
    });
}

function jadwaldokter(arrJadwaldokter) {
    const hari = [
        "Minggu",
        "Senin",
        "Selasa",
        "Rabu",
        "Kamis",
        "Jumat",
        "Sabtu",
    ];

    var setHari = [];
    for (let i = 0; i < hari.length; i++) {
        if (!arrJadwaldokter.includes(hari[i])) {
            setHari.push(i+1);
        }

        if (arrJadwaldokter.includes("On Call")) {
            setHari = [];
        }
    }
    return setHari;
}

function capitalizeEachWord(text) {
    return text.replace(/\b\w/g, function (match) {
        return match.toUpperCase();
    });
}

function readIMG(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $("#previewIMG").attr("src", e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
    }
}

function readIMGOneToOne(input, number) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $("#previewIMG" + number).attr("src", e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
    }
}

function toastifyJS(message, background, color) {
    Toastify({
        text: message,
        duration: 3000,
        newWindow: true,
        // close: true,
        gravity: "top", // `top` or `bottom`
        position: "center", // `left`, `center` or `right`
        stopOnFocus: true, // Prevents dismissing of toast on hover
        style: {
            background: background,
            color: color,
        },
        onClick: function () {}, // Callback after click
    }).showToast();
}
