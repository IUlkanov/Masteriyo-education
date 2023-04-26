import {
	Button,
	ButtonGroup,
	FormControl,
	FormErrorMessage,
	FormLabel,
	Heading,
	Input,
	Stack,
	useToast,
} from '@chakra-ui/react';
import { __ } from '@wordpress/i18n';
import React, { useRef } from 'react';
import { useForm } from 'react-hook-form';
import { useMutation } from 'react-query';
import urls from '../../../back-end/constants/urls';
import API from '../../../back-end/utils/api';

interface PasswordSchema {
	password: string;
	old_password: string;
	confirm_password: string;
}

const PasswordSecurity = () => {
	const {
		register,
		handleSubmit,
		watch,
		formState: { errors },
	} = useForm<PasswordSchema>();

	const password = useRef({});
	password.current = watch('password', '');

	const userAPI = new API(urls.currentUser);
	const toast = useToast();

	const changePassword = useMutation(
		(data: PasswordSchema) => userAPI.store(data),
		{
			onSuccess: () => {
				toast({
					title: __('Password Successfully Changed', 'masteriyo'),
					isClosable: true,
					status: 'success',
				});
			},
			onError: () => {
				toast({
					title: __('Password cannot be changed', 'masteriyo'),
					description: __(
						'Please make sure your old password is correct',
						'masteriyo'
					),
					isClosable: true,
					status: 'error',
				});
			},
		}
	);

	const onSubmit = (data: PasswordSchema) => {
		changePassword.mutate(data);
	};

	return (
		<form onSubmit={handleSubmit(onSubmit)}>
			<Stack spacing="9" w="100%">
				<Heading as="h4" size="lg" fontWeight="medium">
					{__('Change Password', 'masteriyo')}
				</Heading>
				<Stack
					direction="column"
					spacing="8"
					w={{ base: '100%', sm: '100%', md: '50%', lg: '50%' }}>
					<FormControl isInvalid={!!errors?.password}>
						<FormLabel>{__('Current Password', 'masteriyo')}</FormLabel>
						<Input
							type="password"
							{...register('old_password', {
								required: __('This field cannot be empty', 'masteriyo'),
							})}
						/>
						{errors?.old_password && (
							<FormErrorMessage>
								{errors?.old_password.message}
							</FormErrorMessage>
						)}
					</FormControl>
					<FormControl isInvalid={!!errors?.password}>
						<FormLabel>{__('New Password', 'masteriyo')}</FormLabel>
						<Input
							type="password"
							{...register('password', {
								required: __('This field cannot be empty', 'masteriyo'),
							})}
						/>
						{errors?.password && (
							<FormErrorMessage>{errors?.password.message}</FormErrorMessage>
						)}
					</FormControl>
					<FormControl isInvalid={!!errors?.confirm_password}>
						<FormLabel>{__('Confirm New Password', 'masteriyo')}</FormLabel>
						<Input
							type="password"
							{...register('confirm_password', {
								required: __('This field cannot be empty', 'masteriyo'),
								validate: (value) =>
									value === password.current ||
									__('The passwords do not match', 'masteriyo'),
							})}
						/>
						{errors?.confirm_password && (
							<FormErrorMessage>
								{errors?.confirm_password.message}
							</FormErrorMessage>
						)}
					</FormControl>
				</Stack>
			</Stack>

			<Stack py="10">
				<ButtonGroup>
					<Button
						colorScheme="primary"
						rounded="full"
						type="submit"
						px="19"
						textTransform="uppercase">
						{__('Change Password', 'masteriyo')}
					</Button>
				</ButtonGroup>
			</Stack>
		</form>
	);
};

export default PasswordSecurity;
