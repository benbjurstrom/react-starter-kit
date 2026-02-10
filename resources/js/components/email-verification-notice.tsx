import { Link, usePage } from '@inertiajs/react';
import { send } from '@/routes/verification';
import type { SharedData } from '@/types';

export default function EmailVerificationNotice({
    mustVerifyEmail,
    status,
}: {
    mustVerifyEmail: boolean;
    status?: string;
}) {
    const { auth } = usePage<SharedData>().props;

    if (!mustVerifyEmail || auth.user.email_verified_at !== null) {
        return null;
    }

    return (
        <div>
            <p className="-mt-4 text-sm text-muted-foreground">
                Your email address is unverified.{' '}
                <Link
                    href={send()}
                    as="button"
                    className="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                >
                    Click here to resend the verification email.
                </Link>
            </p>

            {status === 'verification-link-sent' && (
                <div className="mt-2 text-sm font-medium text-green-600">
                    A new verification link has been sent to your email address.
                </div>
            )}
        </div>
    );
}
