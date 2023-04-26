import {
	AlertDialog,
	AlertDialogBody,
	AlertDialogContent,
	AlertDialogFooter,
	AlertDialogHeader,
	AlertDialogOverlay,
	Avatar,
	Button,
	ButtonGroup,
	Divider,
	Heading,
	Link,
	List,
	ListIcon,
	ListItem,
	Stack,
	Text,
	useDisclosure,
} from '@chakra-ui/react';
import { __ } from '@wordpress/i18n';
import React, { useState } from 'react';
import { IconType } from 'react-icons';
import {
	BiBook,
	BiBookAlt,
	BiGrid,
	BiHistory,
	BiLogOut,
	BiPlus,
	BiUser,
} from 'react-icons/bi';
import { useQuery } from 'react-query';
import { NavLink, useLocation } from 'react-router-dom';
import urls from '../../back-end/constants/urls';
import { UserSchema } from '../../back-end/schemas';
import API from '../../back-end/utils/api';
import routes from '../constants/routes';
import SkeletonSidebar from '../skeleton/SideBar';
import localized from '../utils/global';

const Sidebar = () => {
	const location = useLocation();
	const userAPI = new API(urls.currentUser);
	const userLogoutAPI = new API(urls.userLogout);
	const userQuery = useQuery<UserSchema>('userProfile', () => userAPI.get());
	const { onClose, onOpen, isOpen } = useDisclosure();
	const [isLogoutLoading, setIsLogoutLoading] = useState(false);
	const cancelRef = React.useRef<any>();
	const navLinkStyles = {
		borderRight: '2px',
		d: 'block',
		borderColor: 'transparent',
		color: 'gray.600',
		fontWeight: 'medium',
		textDecoration: 'none',
		_hover: {
			color: 'primary.500',
		},
	};
	const navActiveStyles = {
		borderColor: 'primary.500',
		color: 'primary.500',
	};

	const redirectLogoutUser = () => {
		setIsLogoutLoading(true);
		userLogoutAPI.get().then((data: { redirect_url: string }) => {
			setIsLogoutLoading(false);
			window.location.href = data?.redirect_url;
		});
	};

	const navText = (text: string, icon: IconType) => (
		<Stack
			direction={{ base: 'column', sm: 'column', md: 'column', lg: 'row' }}
			spacing="4">
			<ListIcon fontSize="md" mr="3" as={icon} />
			<Text display={{ base: 'none', sm: 'none', md: 'block', lg: 'block' }}>
				{text}
			</Text>
		</Stack>
	);

	if (userQuery.isSuccess) {
		return (
			<List
				mr={{ base: '0', sm: '0', md: '4', lg: '6' }}
				flex={{
					base: '0 0 30px',
					sm: '0 0 50px',
					md: '0 0 90px',
					lg: '0 0 200px',
				}}
				borderRight="1px"
				borderRightColor="gray.100"
				spacing="6"
				fontSize="sm"
				fontWeight="medium"
				color="gray.600"
				className="mto-account-sidebar">
				<ListItem>
					<Stack direction="row" align="center" spacing="3">
						<Avatar
							size="sm"
							name={userQuery?.data?.first_name}
							src={userQuery?.data?.profile_image?.url}
							showBorder
							shadow="md"
							ml={{ base: '-5px', sm: '-5px', md: 0, lg: 0 }}
						/>
						<Stack
							direction="column"
							spacing="0"
							display={{ base: 'none', sm: 'none', md: 'block', lg: 'block' }}>
							<Heading as="h5" fontSize="xs" fontWeight="medium">
								{userQuery?.data?.first_name && userQuery?.data?.last_name
									? `${userQuery?.data?.first_name} ${userQuery?.data?.last_name}`
									: userQuery?.data?.username}
							</Heading>
						</Stack>
					</Stack>
				</ListItem>
				<ListItem>
					<Link
						as={NavLink}
						sx={navLinkStyles}
						_activeLink={navActiveStyles}
						to={routes.dashboard}>
						{navText(__('Dashboard', 'masteriyo'), BiGrid)}
					</Link>
				</ListItem>

				{userQuery?.data?.roles.includes('masteriyo_instructor') &&
				'yes' === localized.isInstructorActive ? (
					<>
						<Divider />
						<ListItem>
							<Link sx={navLinkStyles} href={localized.urls.addNewCourse}>
								{navText(__('Add Course', 'masteriyo'), BiPlus)}
							</Link>
						</ListItem>
						<ListItem>
							<Link sx={navLinkStyles} href={localized.urls.myCourses}>
								{navText(__('My Courses', 'masteriyo'), BiBookAlt)}
							</Link>
						</ListItem>
						<Divider />
					</>
				) : null}
				<ListItem>
					<Link
						as={NavLink}
						sx={navLinkStyles}
						_activeLink={navActiveStyles}
						to={routes.courses}>
						{navText(__('Enrolled Courses', 'masteriyo'), BiBook)}
					</Link>
				</ListItem>
				<ListItem>
					<Link
						as={NavLink}
						sx={
							location.pathname?.includes('/user')
								? navActiveStyles
								: navLinkStyles
						}
						_activeLink={navActiveStyles}
						to={routes.user.profile}>
						{navText(__('Profile', 'masteriyo'), BiUser)}
					</Link>
				</ListItem>

				<ListItem>
					<Link
						as={NavLink}
						sx={navLinkStyles}
						_activeLink={navActiveStyles}
						to={routes.order.list}>
						{navText(__('Order History', 'masteriyo'), BiHistory)}
					</Link>
				</ListItem>

				<ListItem>
					<Link sx={navLinkStyles} onClick={onOpen}>
						{navText(__('Logout', 'masteriyo'), BiLogOut)}
					</Link>
				</ListItem>

				<AlertDialog
					isOpen={isOpen}
					onClose={onClose}
					isCentered
					leastDestructiveRef={cancelRef}>
					<AlertDialogOverlay>
						<AlertDialogContent>
							<AlertDialogHeader>
								{__('Log Out', 'masteriyo')}
							</AlertDialogHeader>
							<AlertDialogBody>
								{__('Do you really want to logout?', 'masteriyo')}
							</AlertDialogBody>
							<AlertDialogFooter>
								<ButtonGroup>
									<Button onClick={onClose} variant="outline" ref={cancelRef}>
										{__('Cancel', 'masteriyo')}
									</Button>
									<Button
										colorScheme="red"
										onClick={redirectLogoutUser}
										isLoading={isLogoutLoading}>
										{__('Logout', 'masteriyo')}
									</Button>
								</ButtonGroup>
							</AlertDialogFooter>
						</AlertDialogContent>
					</AlertDialogOverlay>
				</AlertDialog>
			</List>
		);
	}

	return <SkeletonSidebar />;
};

export default Sidebar;
