import {
	Avatar,
	Box,
	Button,
	Heading,
	Icon,
	Image,
	Link,
	Progress,
	Stack,
	Tag,
	TagLabel,
	Text,
} from '@chakra-ui/react';
import { __ } from '@wordpress/i18n';
import dayjs from 'dayjs';
import React from 'react';
import { BiTime } from 'react-icons/bi';
import { IoMdStar, IoMdStarOutline } from 'react-icons/io';
import { useQuery } from 'react-query';
import urls from '../../back-end/constants/urls';
import API from '../../back-end/utils/api';
import { humanizeTime, isNumber } from '../../back-end/utils/utils';
import { CourseProgressMap } from '../../interactive/schemas';
import { MyCoursesSchema } from '../schemas';
import { calculatePercentage } from '../utils/percentage';

interface Props {
	courseData: MyCoursesSchema;
}

const CourseItem: React.FC<Props> = (props) => {
	const { course, started_at } = props.courseData;
	const progressAPI = new API(urls.courseProgress);

	const courseProgressQuery = useQuery<CourseProgressMap>(
		[`courseProgress${course?.id}`, course?.id],
		() => progressAPI.store({ course_id: course?.id }),
		{
			enabled: !!course?.id,
		}
	);

	const renderRatings = () => {
		const average = Number(course.average_rating);
		let stars = [];

		for (let i = 1; i <= 5; i++) {
			if (i <= average) {
				stars.push(<Icon key={i} as={IoMdStar} />);
			} else {
				stars.push(<Icon key={i} as={IoMdStarOutline} />);
			}
		}

		return (
			<Stack direction="row" spacing="0" color="orange.300">
				{stars}
			</Stack>
		);
	};

	const calculateProgress = () => {
		const completed: any = courseProgressQuery?.data?.summary?.total?.completed;
		const pending: any = courseProgressQuery?.data?.summary?.total?.pending;

		if (completed === 0 && pending === 0) {
			return 0;
		} else if (isNumber(completed) && isNumber(pending)) {
			return calculatePercentage(completed, pending + completed);
		} else {
			return 0;
		}
	};

	if (course) {
		const progress = calculateProgress();

		return (
			<Box
				borderWidth="1px"
				borderColor="gray.100"
				className="mto-enrolled-course">
				<Box as="figure" pos="relative">
					<Image
						src={course?.featured_image_url}
						alt={course?.name}
						height="180px"
						width={'100%'}
						objectFit="cover"
					/>
					{course?.difficulty && (
						<Tag
							pos="absolute"
							top="3"
							left="3"
							bg="primary.500"
							color="white"
							borderRadius="full"
							fontWeight="medium"
							size="sm">
							<TagLabel>{course?.difficulty?.name}</TagLabel>
						</Tag>
					)}
				</Box>
				<Stack
					direction="column"
					p="6"
					spacing="3"
					className="mto-enrolled-courses-list_main">
					<Stack direction="column" spacing="6">
						<Stack direction="column" spacing="3">
							<Stack direction="row" spacing="1">
								{course?.categories?.map(
									(category: { id: number; name: string; slug: string }) => (
										<Tag
											key={category.id}
											colorScheme="primary"
											size="sm"
											borderRadius="full"
											border="1px"
											borderColor="gray.200">
											<TagLabel>{category?.name}</TagLabel>
										</Tag>
									)
								)}
							</Stack>

							<Heading as="h3" fontSize="lg">
								{course?.name}
							</Heading>
							<Stack
								direction="row"
								spacing="3"
								align="center"
								justify="space-between">
								<Stack direction="row" spacing="1" align="center">
									<Avatar src={course?.author?.avatar_url} size="xs" />
									<Text fontSize="xs" fontWeight="bold">
										{course?.author?.display_name}
									</Text>
								</Stack>

								{renderRatings()}
							</Stack>
						</Stack>

						<Stack
							direction="row"
							justify="space-between"
							align="center"
							fontSize="sm"
							fontWeight="500"
							color="gray.500">
							<Stack direction="row" spacing="1">
								<Icon as={BiTime} mt="1" />
								<Text>{humanizeTime(course?.duration * 60 * 1000)}</Text>
							</Stack>
							{courseProgressQuery.isSuccess && (
								<Text>
									{progress}
									{__('% Complete', 'masteriyo')}
								</Text>
							)}
						</Stack>
						{courseProgressQuery.isSuccess && (
							<Box mt={5}>
								<Progress
									rounded="full"
									size="xs"
									value={courseProgressQuery?.data?.summary.total.completed}
									max={
										courseProgressQuery?.data?.summary.total.completed +
										courseProgressQuery?.data?.summary.total.pending
									}
								/>
							</Box>
						)}

						<Stack
							direction="row"
							spacing="4"
							justify="space-between"
							align="center"
							color="gray.500"
							fontSize="xs">
							{started_at && (
								<Text>{dayjs(started_at).format('MM/DD/YYYY')}</Text>
							)}
							<Link href={course?.start_course_url}>
								<Button size="sm" borderRadius="full" colorScheme="primary">
									{courseProgressQuery?.data?.status === 'completed'
										? __('Completed', 'masteriyo')
										: courseProgressQuery?.data?.status === 'progress'
										? __('Continue', 'masteriyo')
										: __('Start Course', 'masteriyo')}
								</Button>
							</Link>
						</Stack>
					</Stack>
				</Stack>
			</Box>
		);
	}
	return <></>;
};

export default CourseItem;
