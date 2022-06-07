<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Zigly\ScheduleManagement\Model;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Zigly\ProfessionalGraphQl\Helper\Encryption;

class SaveVendorScheduleManagement implements \Zigly\ScheduleManagement\Api\SaveVendorScheduleManagementInterface {
	public function __construct(
		\Zigly\ScheduleManagement\Model\Data\ScheduleManagementFactory $scheduleManagementFactory,
		\Zigly\ScheduleManagement\Model\ScheduleManagementRepository $scheduleManagementRepository,
		\Zigly\ProfessionalGraphQl\Helper\Encryption $encryption,
		\Zigly\ScheduleManagement\Model\ScheduleManagementFactory $scheduleManagementModelFactory,
		\Zigly\ScheduleManagement\Model\ResourceModel\ScheduleManagement\CollectionFactory $scheduleCollectionFactory
	) {
		$this->scheduleManagementFactory      = $scheduleManagementFactory;
		$this->scheduleManagementModelFactory = $scheduleManagementModelFactory;
		$this->scheduleManagementRepository   = $scheduleManagementRepository;
		$this->encryption                     = $encryption;
		$this->scheduleCollectionFactory      = $scheduleCollectionFactory;
	}

	/**
	 * {@inheritdoc}
	 */
	public function postSaveVendorSchedule($token, $scheduleRequest) {
		$returnMessage = "success";
		$validation = [];
		$professionalExists = $this->encryption->tokenAuthentication($token);
		if (!$professionalExists) {
			throw new NoSuchEntityException(__('Invalid token'));
		}
		if ($professionalExists->getGroomerId() != $scheduleRequest->getProfessionalId()) {
			throw new InputException(__('Not Authorized.'));
		}
		if ($professionalExists->getStatus() != "1") {
			throw new InputException(__('This account is not approved.'));
		}
		if (strtotime($scheduleRequest->getScheduleDate()) < strtotime("NOW")) {
			throw new InputException(__('Can not Update the past day.'));
		}
		try {
			$appliedTo     = $scheduleRequest->getAppliedTo();
			$selectedDate  = $scheduleRequest->getScheduleDate();
			$followingDays = $scheduleRequest->getFollowingDays();
			$applyDays     = [];
			switch ($appliedTo) {
				case 'this_day':
					$applyDays[] = $selectedDate;
					break;

				case 'this_week':
				case 'this_month':
					$applyDays = $this->applyToDays($selectedDate, $appliedTo, null);
					break;

				case 'following_days':
					$applyDays = $this->applyToDays($selectedDate, $appliedTo, $followingDays);
					break;

				default:
					return 'Invalid value passed to appliedTo. It should be this_day, this_week, this_month, following_days.';
					break;
			}
			foreach ($applyDays as $date) {
				foreach ($scheduleRequest->getSchedule() as $schedule) {
					$scheduleManagement = $this->scheduleManagementFactory->create();
					$dataExist          = $this->scheduleCollectionFactory->create()
										->addFieldToFilter('professional_id', $scheduleRequest->getProfessionalId())
						->addFieldToFilter('schedule_date', $date)
						->addFieldToFilter('slot', $schedule->getSlot())->getData();
					$checkForBooking = $this->scheduleCollectionFactory->create()
											->addFieldToFilter('professional_id', $scheduleRequest->getProfessionalId())
						->addFieldToFilter('schedule_date', $date)
						->addFieldToFilter('booking_id', ["neq" => 'NULL'])->getData();
					// if (strtotime($date." ".explode("-",$schedule->getSlot())[0]) < strtotime("now +4 hours")) {
					// 	$slotMessage ["message"] = 'Schedule can not be updated before 4 hours';
					// 	$slotMessage ["date"] = $scheduleRequest->getScheduleDate();
					// 	$slotMessage ["slot"] = $scheduleRequest->getSlot();
					// 	$slotMessage ["status"] = "failed";
					// 	$validation [] = $slotMessage;
					// }
					if (count($dataExist)) {
						$scheduleModel = $this->scheduleManagementModelFactory->create();
						$scheduleModel->load($dataExist[0]["schedulemanagement_id"]);
						if ($scheduleModel->getBookingId()) {
							$slotMessage ["message"] = 'Schedule can not be updated, Since you have booking on this slot ('.$scheduleModel->getScheduleDate()." - ".$scheduleModel->getSlot().')';
							$slotMessage ["date"] = $scheduleModel->getScheduleDate();
							$slotMessage ["slot"] = $scheduleModel->getSlot();
							$slotMessage ["status"] = "failed";
							$validation [] = $slotMessage;
						}
						if (
							count($checkForBooking) &&
							(
								$scheduleRequest->getWorkingMode() !== $scheduleModel->getWorkingMode() ||
								($scheduleRequest->getAvailability() == "0" && $scheduleModel->getAvailability() == "1")
							)
						) {
							$slotMessage ["message"] = 'Schedule can not be updated, Since you have booking on this slot ('.$scheduleModel->getScheduleDate();
							$slotMessage ["date"] = $scheduleModel->getScheduleDate();
							$slotMessage ["slot"] = $scheduleModel->getSlot();
							$slotMessage ["status"] = "failed";
							$validation [] = $slotMessage;
						}
					}
				}
			}
			if (count($validation)) {
				return $validation;
			}
			foreach ($applyDays as $date) {
				foreach ($scheduleRequest->getSchedule() as $schedule) {
					try {
						$scheduleManagement = $this->scheduleManagementFactory->create();
						$dataExist          = $this->scheduleCollectionFactory->create()
						                  ->addFieldToFilter('professional_id', $scheduleRequest->getProfessionalId())
							->addFieldToFilter('schedule_date', $date)
							->addFieldToFilter('slot', $schedule->getSlot())->getData();
						$checkForBooking = $this->scheduleCollectionFactory->create()
						                        ->addFieldToFilter('professional_id', $scheduleRequest->getProfessionalId())
							->addFieldToFilter('schedule_date', $date)
							->addFieldToFilter('booking_id', ["neq" => 'NULL'])->getData();
						if (!count($dataExist)) {
							$scheduleManagement->setProfessionalId($scheduleRequest->getProfessionalId());
							$scheduleManagement->setScheduleDate($date);
							$scheduleManagement->setWorkingMode($scheduleRequest->getWorkingMode());
							$scheduleManagement->setSlot($schedule->getSlot());
							$scheduleManagement->setSlotStartTime(strtotime($date." ".explode("-",$schedule->getSlot())[0]));
							$scheduleManagement->setBookingId($schedule->getBookingId());
							$scheduleManagement->setAvailability($schedule->getAvailability());
							$this->scheduleManagementRepository->save($scheduleManagement);
						} else {
							$scheduleModel = $this->scheduleManagementModelFactory->create();
							$scheduleModel->load($dataExist[0]["schedulemanagement_id"]);
							if ($scheduleModel->getBookingId()) {
								throw new InputException(__('Schedule can not be updated, Since you have booking on this slot ('.$scheduleModel->getScheduleDate()." - ".$scheduleModel->getSlot().')'));
							}
							if (
								count($checkForBooking) &&
								(
									$scheduleRequest->getWorkingMode() !== $scheduleModel->getWorkingMode() ||
									($scheduleRequest->getAvailability() == "0" && $scheduleModel->getAvailability() == "1")
								)
							) {
								throw new InputException(__('Schedule can not be updated, Since you have booking on this day ('.$scheduleModel->getScheduleDate().")"));
							}
							$scheduleModel->setWorkingMode($scheduleRequest->getWorkingMode());
							$scheduleModel->setBookingId($schedule->getBookingId());
							$scheduleModel->setAvailability($schedule->getAvailability());
							$scheduleModel->save();
						}
					} catch (\Exception $e) {
						$returnMessage .= "\n".$e->getMessage();
						continue;
					}
				}
			}
		} catch (\Exception $e) {
			return $e->getMessage();
		}

		return $returnMessage;
	}

	public function applyToDays($selectedDate, $appliedTo, $followingDays = []) {
		$day     = [];
		$selectedDaytime      = strtotime($selectedDate);
		$day[]   = $selectedDate;
		$lastDay = "";
		if ($appliedTo == "this_week") {
			$lastDay  = date('Y-m-d', strtotime('Saturday this Week', $selectedDaytime));
		} else if ($appliedTo == "this_month") {
			$lastDay  = date("Y-m-d", strtotime("Last Day of this Month", $selectedDaytime));
		} else if ($appliedTo == "following_days") {
			$lastDay  = date("Y-m-d", strtotime("Last Day of this Month ", strtotime("$selectedDate + 6 Months")));
		}
		$nextday = $selectedDate;
		while (strtotime($selectedDate) <= strtotime($lastDay)) {
            $nextday = date("Y-m-d", strtotime("$nextday +1 day"));
            if ($appliedTo == "following_days" && !in_array(date("D", strtotime("$nextday")),$followingDays)) {
                continue;
            }
            $day[]   = $nextday;
			if ($lastDay == $nextday) {
				break;
			}
		}
		return $day;
	}
}
