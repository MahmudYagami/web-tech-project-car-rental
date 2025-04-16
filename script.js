function showDetail(type) {
    switch (type) {
      case 'bookings':
        alert("Showing booking details...");
        break;
      case 'revenue':
        alert("Showing revenue analytics...");
        break;
      case 'users':
        alert("Showing user statistics...");
        break;
      default:
        alert("Unknown widget.");
    }
  }
  