
document.addEventListener('DOMContentLoaded', () => {
  // Tabs
  const processTab = document.getElementById('tab-process');
  const transitTab = document.getElementById('tab-ontransit');
  const deliveredTab = document.getElementById('tab-delivered');
  const onProcess = document.getElementById('process-table');
  const onTransit = document.getElementById('transit-table');
  const onDelivered = document.getElementById('delivered-table');


  processTab.addEventListener('click', () => {
    onProcess.classList.remove('hidden');
    onTransit.classList.add('hidden');
    onDelivered.classList.add('hidden');
    processTab.classList.add('text-green-600', 'border-b-4', 'border-green-600');
    transitTab.classList.remove('text-green-600', 'border-b-4', 'border-green-600');
    deliveredTab.classList.remove('text-green-600', 'border-b-4', 'border-green-600');
  });

  transitTab.addEventListener('click', () => {
    onProcess.classList.add('hidden');
    onTransit.classList.remove('hidden');
    onDelivered.classList.add('hidden');
    processTab.classList.remove('text-green-600', 'border-b-4', 'border-green-600');
    transitTab.classList.add('text-green-600', 'border-b-4', 'border-green-600');
    deliveredTab.classList.remove('text-green-600', 'border-b-4', 'border-green-600');
    processTab.classList.add('text-gray-600');
  });

  deliveredTab.addEventListener('click', () => {
    onProcess.classList.add('hidden');
    onTransit.classList.add('hidden');
    onDelivered.classList.remove('hidden');
    processTab.classList.remove('text-green-600', 'border-b-4', 'border-green-600');
    transitTab.classList.remove('text-green-600', 'border-b-4', 'border-green-600');
    deliveredTab.classList.add('text-green-600', 'border-b-4', 'border-green-600');
    processTab.classList.add('text-gray-600');
  });
});

function viewProcess(orderId) {
  // Open the modal
  document.getElementById('processdetailsModal').classList.remove('hidden');
  document.getElementById('processmodalContent').innerHTML = "Loading...";

  // Set orderId for Deliver button
  const deliverButton = document.getElementById('deliverButton');
  deliverButton.setAttribute('data-order-id', orderId);

  document.getElementById('deliverButton').addEventListener('click', function() {
    const orderId = this.getAttribute('data-order-id');

    fetch('update_delivery_transit.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          order_id: orderId
        }),
      })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert('Delivery status updated to On-transit.');
          closeModal();
          location.reload(); // Refresh the page to show updated status
        } else {
          alert('Failed to update status: ' + data.message);
        }
      })
      .catch((error) => {
        console.error('Error updating status:', error);
        alert('Error updating status.');
      });
  });

  // Fetch details via AJAX
  $.ajax({
    url: 'fetch_delivery_details.php',
    type: 'GET',
    data: {
      order_id: orderId
    },
    success: function(response) {
      document.getElementById('processmodalContent').innerHTML = response;
    },
    error: function() {
      document.getElementById('processmodalContent').innerHTML = "Error fetching details.";
    }
  });
}

function closeModal() {
  document.getElementById('processdetailsModal').classList.add('hidden');
}


//Transit
function viewTransit(orderId) {
  // Open the modal
  document.getElementById('transitdetailsModal').classList.remove('hidden');
  document.getElementById('transitmodalContent').innerHTML = "Loading...";

  // Set orderId for Deliver button
  const deliverButton = document.getElementById('deliveredButton');
  deliverButton.setAttribute('data-order-id', orderId);

  document.getElementById('deliveredButton').addEventListener('click', function() {
    const orderId = this.getAttribute('data-order-id');

    fetch('update_delivery_delivered.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        }, 
        body: JSON.stringify({
          order_id: orderId
        }),
      })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert('Delivery status updated to Delivered.');
          closetransitModal();
          location.reload(); // Refresh the page to show updated status
        } else {
          alert('Failed to update status: ' + data.message);
        }
      })
      .catch((error) => {
        console.error('Error updating status:', error);
        alert('Error updating status.');
      });
  });

  // Fetch details via AJAX
  $.ajax({
    url: 'fetch_delivery_details.php',
    type: 'GET',
    data: {
      order_id: orderId
    },
    success: function(response) {
      document.getElementById('transitmodalContent').innerHTML = response;
    },
    error: function() {
      document.getElementById('transitmodalContent').innerHTML = "Error fetching details.";
    }
  });
}

function closetransitModal() {
  document.getElementById('transitdetailsModal').classList.add('hidden');
}



//Delivered
function viewDelivered(orderId) {
  // Open the modal
  document.getElementById('deliverdetailsModal').classList.remove('hidden');
  document.getElementById('delivermodalContent').innerHTML = "Loading...";

  // Set orderId for Deliver button
  const deliverButton = document.getElementById('completedButton');
  deliverButton.setAttribute('data-order-id', orderId);

  document.getElementById('completedButton').addEventListener('click', function() {
    const orderId = this.getAttribute('data-order-id');

    fetch('update_delivery_completed.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          order_id: orderId
        }),
      })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert('Order status updated to Completed.');
          closedeliverModal();
          location.reload(); // Refresh the page to show updated status
        } else {
          alert('Failed to update status: ' + data.message);
        }
      })
      .catch((error) => {
        console.error('Error updating status:', error);
        alert('Error updating status.');
      });
  });

  // Fetch details via AJAX
  $.ajax({
    url: 'fetch_delivery_details.php',
    type: 'GET',
    data: {
      order_id: orderId
    },
    success: function(response) {
      document.getElementById('delivermodalContent').innerHTML = response;
    },
    error: function() {
      document.getElementById('delivermodalContent').innerHTML = "Error fetching details.";
    }
  });
}

function closedeliverModal() {
  document.getElementById('deliverdetailsModal').classList.add('hidden');
}



//Notif and account 
document.getElementById('notificationButton').addEventListener('click', function() {
  const dropdown = document.getElementById('notificationDropdown');
  dropdown.classList.toggle('hidden');
});

document.getElementById('accountButton').addEventListener('click', function() {
  const popper = document.getElementById('accountPopper');
  popper.classList.toggle('hidden');
});


window.addEventListener('click', function(event) {
  const dropdown = document.getElementById('notificationDropdown');
  const popper = document.getElementById('accountPopper');

  if (!event.target.closest('#notificationButton')) {
    dropdown.classList.add('hidden');
  }
  if (!event.target.closest('#accountButton')) {
    popper.classList.add('hidden');
  }
});
