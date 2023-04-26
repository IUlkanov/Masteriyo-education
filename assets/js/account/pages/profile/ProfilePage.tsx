import { Avatar, Button, Flex, Heading, Stack, Text } from '@chakra-ui/react';
import { __ } from '@wordpress/i18n';
import React from 'react';
import { BiEdit } from 'react-icons/bi';
import { useQuery } from 'react-query';
import { Link } from 'react-router-dom';
import urls from '../../../back-end/constants/urls';
import { UserSchema } from '../../../back-end/schemas';
import API from '../../../back-end/utils/api';
import routes from '../../constants/routes';
import ProfileSkeleton from '../../skeleton/Profile';

const ProfilePage = () => {
	const userAPI = new API(urls.currentUser);

	const { data, isSuccess } = useQuery<UserSchema>('userProfile', () =>
		userAPI.get()
	);

	const userInfo = (key: string, value: string = '', bg: boolean = false) => (
		<Stack
			p="12px 40px"
			bg={bg ? '#f8f9fa' : ''}
			marginTop="0px !important"
			direction={{ base: 'column', sm: 'row', md: 'row', lg: 'row' }}
			justifyContent="space-between"
			className="mto-profile-user-detail">
			<Text className="mto-profile-user-detail_left">
				{__(key, 'masteriyo')}
			</Text>
			<Text className="mto-profile-user-detail_right">{value}</Text>
		</Stack>
	);

	if (isSuccess) {
		return (
			<Stack
				direction="column"
				spacing="8"
				width="full"
				className="mto-profile-wrapper">
				<Flex justify={'space-between'}>
					<Heading as="h4" size="md" fontWeight="bold" color="primary.900">
						{__('Profile', 'masteriyo')}
					</Heading>
					<Link to={routes.user.edit}>
						<Button
							leftIcon={<BiEdit size={15} color={'gray.500'} />}
							color="gray.500"
							size="sm"
							borderRadius="full"
							borderColor="gray.400"
							variant="outline">
							{__('Edit Profile', 'masteriyo')}
						</Button>
					</Link>
				</Flex>
				<Stack
					className="mto-profile-details"
					direction={{ base: 'column', sm: 'column', md: 'row', lg: 'row' }}
					spacing="6">
					<Avatar
						src={data?.profile_image?.url}
						size="2xl"
						className="mto-profile-user-avatar"
					/>

					<Stack
						direction="column"
						spacing="8"
						flex="1"
						className="mto-profile-wrapper">
						<Text as="h3" fontSize="4xl" fontWeight="medium">
							{data?.first_name && data?.last_name
								? `${data?.first_name} ${data?.last_name}`
								: data?.username}
						</Text>

						<Stack direction="column" className="mto-profile-user-info">
							{userInfo(__('First Name', 'masteriyo'), data?.first_name, true)}
							{userInfo(__('Last Name', 'masteriyo'), data?.last_name)}
							{userInfo(__('Username', 'masteriyo'), data?.username, true)}
							{userInfo(__('Email', 'masteriyo'), data?.email)}
						</Stack>

						<Heading fontSize="lg" px="6">
							{__('Billing', 'masteriyo')}
						</Heading>

						<Stack direction="column">
							{userInfo(
								__('First Name', 'masteriyo'),
								data?.billing?.first_name,
								true
							)}
							{userInfo(__('Last Name', 'masteriyo'), data?.billing?.last_name)}
							{userInfo(
								__('Contact Number', 'masteriyo'),
								data?.billing?.phone,
								true
							)}
							{userInfo(
								__('Country', 'masteriyo'),
								data?.billing?.country_name
							)}
							{userInfo(
								__('State', 'masteriyo'),
								data?.billing?.state_name,
								true
							)}
							{userInfo(__('City', 'masteriyo'), data?.billing?.city)}
							{userInfo(
								__('Zip Code', 'masteriyo'),
								data?.billing?.postcode,
								true
							)}
							{userInfo(__('Address 1', 'masteriyo'), data?.billing?.address_1)}
							{userInfo(
								__('Address 2', 'masteriyo'),
								data?.billing?.address_2,
								true
							)}
							{userInfo(
								__('Company Name', 'masteriyo'),
								data?.billing?.company_name
							)}
							{userInfo(
								__('Company VAT Number', 'masteriyo'),
								data?.billing?.company_id,
								true
							)}
						</Stack>
					</Stack>
				</Stack>
			</Stack>
		);
	}

	return <ProfileSkeleton />;
};

export default ProfilePage;
