<template>
    <div class="card">
        <div class="card-header">
            <h4>
                Booking List
                <router-link to="/booking/create" class="btn btn-primary float-end">
                    Add Booking
                </router-link>
            </h4>
        </div>
        <div class="card-body">
            <div v-if="success.length > 0" class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>{{ success }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Title</th>
                        <th>Details</th>
                        <th>Booking Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody v-if="booking.length > 0">
                    <tr v-for="(data, index) in booking" :key="index">
                        <td>{{ data.id }}</td>
                        <td>{{ data.User }}</td>
                        <td>{{ data.meeting_title }}</td>
                        <td>{{ data.description }}</td>
                        <td>{{ data.booking_date }}</td>
                        <td>{{ data.start_time }}</td>
                        <td>{{ data.end_time }}</td>
                        <td>
                            <span :class="statusClass(data.status)">
                                {{ data.status }}
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-warning me-2">
                                <router-link :to="'/booking/' + data.id + '/edit'" class="text-white">
                                    Edit
                                </router-link>
                            </button>
                            <button @click="deleteBooking(data.id)" type="button" class="btn btn-danger me-2">
                                Delete
                            </button>
                            <button @click="cancelBooking(data)" type="button" class="btn btn-secondary">
                                Cancel
                            </button>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td colspan="9"> <strong>Loading..........</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'BookingList',
    data() {
        return {
            booking: [],
            success: ''
        };
    },
    mounted() {
        this.getBooking();
    },
    methods: {
        getBooking() {
            axios.get('http://localhost:8000/api/booking')
                .then(response => {
                    this.booking = response.data.data.data;
                })
                .catch(error => {
                    console.error('Error fetching bookings:', error.response ? error.response.data : error.message);
                });
        },

        editBooking(bookingID, bookingData) {
            axios.put(`http://localhost:8000/api/booking/${bookingID}/edit`, bookingData)
                .then(response => {
                    console.log('Booking updated successfully:', response.data);
                    this.getBooking();
                })
                .catch(error => {
                    console.error('There was an error editing the booking:', error.response ? error.response.data : error.message);
                });
        },

        cancelBooking(booking) {
            if (confirm('Are you sure you want to cancel this booking?')) {
                axios.delete(`http://localhost:8000/api/booking/${booking.id}/cancel`)
                    .then(response => {
                        console.log('Booking cancelled successfully:', response.data);
                        booking.status = 'cancelled'; // Update status locally
                    })
                    .catch(error => {
                        console.error('Error cancelling the booking:', error.response ? error.response.data : error.message);
                        alert('Error cancelling the booking: ' + error.message); // Display error to user
                    });
            }
        },

        deleteBooking(bookingID) {
            if (confirm('Are you sure you want to delete this booking?')) {
                axios.delete(`http://localhost:8000/api/booking/${bookingID}/delete`)
                    .then(response => {
                        this.success = response.data.message;
                        this.getBooking();
                    })
                    .catch(error => {
                        if (error.response && error.response.status === 404) {
                            alert(error.response.data.errors);
                        } else {
                            console.error('Error deleting booking:', error.response ? error.response.data : error.message);
                        }
                    });
            }
        },

        statusClass(status) {
            switch (status) {
                case 'confirmed':
                    return 'badge bg-success';
                case 'cancelled':
                    return 'badge bg-danger';
                default:
                    return 'badge bg-secondary';
            }
        },
    },
};
</script>

<style scoped>
/* Add any scoped styles here */
</style>
