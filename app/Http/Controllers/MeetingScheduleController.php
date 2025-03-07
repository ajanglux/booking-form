<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Repositories\BookingRepository;
use App\Http\Requests\BookingCreateRequest;
use App\Models\MeetingSchedule; // Adjusted model namespace
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MeetingScheduleController extends Controller
{
    use ResponseTrait;

    protected $scheduleBookingRepository;

    public function __construct(BookingRepository $bookingRepository)
    {
        $this->scheduleBookingRepository = $bookingRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            return $this->responseSuccess($this->scheduleBookingRepository->getAll(), 'Booking fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookingCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['start_time'] = $data['booking_date'].' '.$data['start_time'];
        $data['end_time'] = $data['booking_date'].' '.$data['end_time'];
        $data['status'] = 'pending'; // Default status is set here

        try {
            return $this->responseSuccess($this->scheduleBookingRepository->create($data), 'Booking created successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->scheduleBookingRepository->getById($id), 'Booking fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->scheduleBookingRepository->update($id, $request->all()), 'Booking updated successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->scheduleBookingRepository->delete($id), 'Booking deleted successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * Cancel a booking.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function cancel(int $id): JsonResponse
    {
        try {
            // Find the booking by ID
            $booking = MeetingSchedule::findOrFail($id);
            
            // Perform cancellation logic, for example, setting a status
            $booking->status = 'cancelled';
            $booking->save();

            // Return success response
            return $this->responseSuccess($booking, 'Booking cancelled successfully.');
        } catch (ModelNotFoundException $exception) {
            // Return error if booking not found
            return $this->responseError([], 'Booking not found.', 404);
        } catch (\Exception $exception) {
            // Return error for any other exceptions
            return $this->responseError([], $exception->getMessage(), 500);
        }
    }
}
