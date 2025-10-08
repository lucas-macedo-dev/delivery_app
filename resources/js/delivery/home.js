window.searchData = async function () {
    let startDate = document.getElementById('startDate').value;
    let endDate   = document.getElementById('endDate').value;

    let response = await fetch(`./home/searchData?startDate=${startDate}&endDate=${endDate}`, {
        headers: window.ajax_headers
    });

    let data = await response.json();
    console.log(data);
    if (data.status === 200 && data.data) {

    } else {

    }
}
