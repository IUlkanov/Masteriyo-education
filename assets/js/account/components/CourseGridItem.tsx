import {
	Box,
	Button,
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
import { useQuery } from 'react-query';
import urls from '../../back-end/constants/urls';
import API from '../../back-end/utils/api';
import { humanizeTime } from '../../back-end/utils/utils';
import { CourseProgressMap } from '../../interactive/schemas';
import { MyCoursesSchema } from '../schemas';

interface Props {
	course: MyCoursesSchema;
}

const CourseGridItem: React.FC<Props> = (props) => {
	const { course, started_at } = props.course;
	const progressAPI = new API(urls.courseProgress);

	const courseProgressQuery = useQuery<CourseProgressMap>(
		[`courseProgress${course?.id}`, course?.id],
		() => progressAPI.store({ course_id: course?.id }),
		{
			enabled: !!course?.id,
		}
	);

	return (
		<Box
			p="5"
			border="1px"
			borderColor="gray.100"
			className="mto-continue-course">
			<Stack
				direction={{ base: 'column', sm: 'column', md: 'column', lg: 'row' }}
				spacing="10"
				align={{ base: 'left', sm: 'left', md: 'left', lg: 'center' }}
				className="mto-continue-course-content">
				<Stack
					direction={{ base: 'column', sm: 'column', md: 'row', lg: 'row' }}
					spacing="4"
					mb={4}
					className="mto-continue-course-content_left">
					<Image
						w={{ base: '100%', sm: '100%', md: '80px', lg: '80px' }}
						src={course?.featured_image_url}
						h={{ base: '170px', sm: '180px', md: '50px', lg: '50px' }}
					/>
					<Stack direction="column" spacing="2" w="120px">
						<Stack
							direction={{ base: 'column', sm: 'row', md: 'row', lg: 'row' }}
							spacing="1"
							w={{
								sm: '20rem',
								md: '30rem',
								lg: '37rem',
							}}>
							{course?.categories?.map(
								(category: { id: number; name: string; slug: string }) => (
									<Tag
										colorScheme="primary"
										key={category.id}
										size="sm"
										borderRadius="full"
										border="1px"
										borderColor="gray.200">
										<TagLabel>{category?.name}</TagLabel>
									</Tag>
								)
							)}
						</Stack>
						<Text fontSize="md" fontWeight="bold">
							{course?.name}
						</Text>

						<Stack
							direction="row"
							spacing="0.5"
							color="gray.700"
							align="center">
							<Icon as={BiTime} />
							<Text fontSize="xs" fontWeight="medium">
								{humanizeTime(course?.duration * 60 * 1000)}
							</Text>
						</Stack>
					</Stack>
				</Stack>

				<Stack
					direction={{ base: 'column', sm: 'column', md: 'column', lg: 'row' }}
					align={{ base: 'left', sm: 'left', md: 'left', lg: 'center' }}
					justify="space-between"
					flex="1"
					className="mto-continue-course-content_right">
					<Stack
						direction="column"
						justifyContent="center"
						spacing="2"
						flex="1">
						{courseProgressQuery.isSuccess && (
							<Stack direction="row" spacing="4" align="center">
								<Progress
									size="sm"
									rounded="full"
									w="full"
									value={courseProgressQuery?.data?.summary.total.completed}
									max={
										courseProgressQuery?.data?.summary.total.completed +
										courseProgressQuery?.data?.summary.total.pending
									}
								/>
								<Text fontWeight="medium" fontSize="sm" w="3xs">
									{courseProgressQuery?.data?.summary?.total?.completed === 0 &&
									courseProgressQuery?.data?.summary?.total?.pending === 0
										? 0
										: Math?.round(
												(courseProgressQuery?.data?.summary?.total?.completed /
													(courseProgressQuery?.data?.summary?.total?.pending +
														courseProgressQuery?.data?.summary?.total
															?.completed)) *
													100
										  )}
									{__('% Complete', 'masteriyo')}
								</Text>
							</Stack>
						)}
						{started_at && (
							<Text color="gray.500" fontSize="xs">
								{__('Started on ', 'masteriyo')}
								{dayjs(started_at).format('MM/DD/YYYY')}
							</Text>
						)}
					</Stack>
					<Link
						href={course?.start_course_url}
						style={{ width: 'fit-content' }}>
						<Button
							colorScheme="primary"
							boxShadow="none"
							size="sm"
							borderRadius="full"
							textTransform="uppercase">
							{__('Continue', 'masteriyo')}
						</Button>
					</Link>
				</Stack>
			</Stack>
		</Box>
	);
};

export default CourseGridItem;
