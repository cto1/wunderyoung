<?php

class EmailTemplates {


    /**
     * Prepare email content for login verification
     */
    public static function prepareLoginEmail($email, $loginLink) {      
        return [
            'subject' => 'Your ExactSum Login Link',
            'textContent' => "Click this link to login: {$loginLink}\n\nIf the link doesn't work, copy and paste this URL into your browser:\n{$loginLink}\n\nThis link will expire in 1 hour.",
            'htmlContent' => "<!DOCTYPE html>
                              <html lang='en'>
                              <head>
                                <meta charset='UTF-8'>
                                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                                <title>ExactSum Login Link</title>
                              </head>
                              <body style='margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, Helvetica, sans-serif;'>
                                <table cellpadding='0' cellspacing='0' border='0' width='100%' style='background-color: #f4f4f4; margin: 0; padding: 0;'>
                                  <tr>
                                    <td align='center' style='padding: 20px;'>
                                      <table cellpadding='0' cellspacing='0' border='0' width='600' style='max-width: 600px; background-color: #ffffff; border-radius: 8px;'>
                                        <tr>
                                          <td style='padding: 30px; text-align: center;'>
                                            <table cellpadding='0' cellspacing='0' border='0' width='100%'>
                                              <tr>
                                                <td style='text-align: center; padding-bottom: 20px;'>
                                                  <h1 style='margin: 0; font-size: 24px; font-weight: bold; color: #333333; font-family: Arial, Helvetica, sans-serif;'>Your ExactSum Login Link</h1>
                                                </td>
                                              </tr>
                                              <tr>
                                                <td style='text-align: center; padding: 20px 0;'>
                                                  <table cellpadding='0' cellspacing='0' border='0' style='margin: 0 auto;'>
                                                    <tr>
                                                      <td style='background-color: #4639a1; border-radius: 5px; text-align: center;'>
                                                        <a href='" . htmlspecialchars($loginLink, ENT_QUOTES, 'UTF-8') . "' style='display: inline-block; padding: 12px 24px; color: #ffffff; text-decoration: none; font-size: 16px; font-weight: bold; font-family: Arial, Helvetica, sans-serif;'>Login to ExactSum</a>
                                                      </td>
                                                    </tr>
                                                  </table>
                                                </td>
                                              </tr>
                                              <tr>
                                                <td style='text-align: center; padding-top: 20px;'>
                                                  <p style='margin: 0 0 10px 0; font-size: 14px; color: #666666; font-family: Arial, Helvetica, sans-serif;'>If the button doesn't work, copy and paste this URL:</p>
                                                  <table cellpadding='0' cellspacing='0' border='0' width='100%'>
                                                    <tr>
                                                      <td style='padding: 10px; background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; text-align: center;'>
                                                        <p style='margin: 0; font-family: Courier, monospace; font-size: 12px; color: #495057; word-break: break-all;'>" . htmlspecialchars($loginLink, ENT_QUOTES, 'UTF-8') . "</p>
                                                      </td>
                                                    </tr>
                                                  </table>
                                                </td>
                                              </tr>
                                              <tr>
                                                <td style='text-align: center; padding-top: 20px;'>
                                                  <p style='margin: 0; font-size: 14px; color: #666666; font-family: Arial, Helvetica, sans-serif;'>This link will expire in 1 hour.</p>
                                                </td>
                                              </tr>
                                            </table>
                                          </td>
                                        </tr>
                                      </table>
                                    </td>
                                  </tr>
                                </table>
                              </body>
                              </html>"
        ];
    }

  


    /**
     * Prepare email content for OCR processing completion
     */
    public static function prepareOcrCompletionEmail($fileName, $projectName, $loginLink, $totalTransactions = null) {
        // Ensure parameters are not null to avoid deprecated warnings
        $fileName = $fileName ?? 'Document';
        $projectName = $projectName ?? 'Project';
        $loginLink = $loginLink ?? '#';
        
        $subject = "AI Processing Complete - {$fileName} for {$projectName}";
        $transactionText = $totalTransactions ? " and extracted {$totalTransactions} transactions" : '';
        
        return [
            'subject' => $subject,
            'textContent' => "Great news! We've successfully processed your document '{$fileName}' in project '{$projectName}'{$transactionText}.\n\n" .
                           "Your bank statement analysis is now ready for review. Click the link below to access your analysis:\n" .
                           "{$loginLink}\n\n" .
                           "What's next?\n" .
                           "• Review extracted transaction data\n" .
                           "• Generate detailed reports\n" .
                           "• Export results to Excel\n" .
                           "• Process additional documents for comprehensive analysis\n\n" .
                           "If you have more documents to process, we recommend uploading and processing them all at once for the most comprehensive analysis.\n\n" .
                           "Best regards,\nThe ExactSum Team",
            'htmlContent' => "<!DOCTYPE html>
                            <html lang='en'>
                            <head>
                              <meta charset='UTF-8'>
                              <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                              <title>AI Processing Complete</title>
                            </head>
                            <body style='margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, Helvetica, sans-serif;'>
                              <table cellpadding='0' cellspacing='0' border='0' width='100%' style='background-color: #f4f4f4; margin: 0; padding: 0;'>
                                <tr>
                                  <td align='center' style='padding: 20px;'>
                                    <table cellpadding='0' cellspacing='0' border='0' width='600' style='max-width: 600px; background-color: #ffffff; border-radius: 8px;'>
                                      <tr>
                                        <td style='padding: 30px;'>
                                          <table cellpadding='0' cellspacing='0' border='0' width='100%'>
                                            <!-- Header with success icon -->
                                            <tr>
                                              <td style='text-align: center; padding-bottom: 20px;'>
                                                <div style='display: inline-block; width: 60px; height: 60px; background-color: #22c55e; border-radius: 50%; margin-bottom: 15px; position: relative;'>
                                                  <div style='position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 24px; font-weight: bold;'>✓</div>
                                                </div>
                                                <h1 style='margin: 0; font-size: 24px; font-weight: bold; color: #333333; font-family: Arial, Helvetica, sans-serif;'>AI Processing Complete!</h1>
                                              </td>
                                            </tr>
                                            <!-- Greeting and document info -->
                                            <tr>
                                              <td style='text-align: center; padding-bottom: 20px;'>
                                                <p style='margin: 0; font-size: 16px; color: #333333; font-family: Arial, Helvetica, sans-serif; line-height: 1.5;'>Great news! We've successfully processed your document <span style='color: #4639a1; font-weight: bold;'>" . htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8') . "</span> in project <span style='color: #4639a1; font-weight: bold;'>" . htmlspecialchars($projectName, ENT_QUOTES, 'UTF-8') . "</span>{$transactionText}.</p>
                                              </td>
                                            </tr>
                                            <!-- CTA Button -->
                                            <tr>
                                              <td style='text-align: center; padding: 20px 0;'>
                                                <table cellpadding='0' cellspacing='0' border='0' style='margin: 0 auto;'>
                                                  <tr>
                                                    <td style='background-color: #4639a1; border-radius: 5px; text-align: center;'>
                                                      <a href='" . htmlspecialchars($loginLink, ENT_QUOTES, 'UTF-8') . "' style='display: inline-block; padding: 12px 24px; color: #ffffff; text-decoration: none; font-size: 16px; font-weight: bold; font-family: Arial, Helvetica, sans-serif;'>View Analysis Results</a>
                                                    </td>
                                                  </tr>
                                                </table>
                                              </td>
                                            </tr>
                                            <!-- What's next section -->
                                            <tr>
                                              <td style='padding: 20px 0;'>
                                                <table cellpadding='0' cellspacing='0' border='0' width='100%'>
                                                  <tr>
                                                    <td style='padding: 15px; background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px;'>
                                                      <h3 style='margin: 0 0 10px 0; font-size: 18px; color: #333333; font-family: Arial, Helvetica, sans-serif;'>What's next?</h3>
                                                      <ul style='margin: 0; padding-left: 20px; font-size: 14px; color: #666666; font-family: Arial, Helvetica, sans-serif; line-height: 1.6;'>
                                                        <li>Review extracted transaction data</li>
                                                        <li>Generate detailed reports</li>
                                                        <li>Export results to Excel</li>
                                                        <li>Process additional documents for comprehensive analysis</li>
                                                      </ul>
                                                    </td>
                                                  </tr>
                                                </table>
                                              </td>
                                            </tr>
                                            <!-- Recommendation -->
                                            <tr>
                                              <td style='padding: 20px 0;'>
                                                <table cellpadding='0' cellspacing='0' border='0' width='100%'>
                                                  <tr>
                                                    <td style='padding: 15px; background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px;'>
                                                      <p style='margin: 0; font-size: 14px; color: #856404; font-family: Arial, Helvetica, sans-serif; line-height: 1.5;'>
                                                        <strong>💡 Tip:</strong> If you have more documents to process, we recommend uploading and processing them all at once for the most comprehensive analysis.
                                                      </p>
                                                    </td>
                                                  </tr>
                                                </table>
                                              </td>
                                            </tr>
                                            <!-- Footer -->
                                            <tr>
                                              <td style='text-align: center; padding-top: 30px; border-top: 1px solid #e9ecef;'>
                                                <p style='margin: 0; font-size: 14px; color: #666666; font-family: Arial, Helvetica, sans-serif;'>Best regards,<br>The ExactSum Team</p>
                                              </td>
                                            </tr>
                                          </table>
                                        </td>
                                      </tr>
                                    </table>
                                  </td>
                                </tr>
                              </table>
                            </body>
                            </html>"
        ];
    }

    /**
     * Prepare email content for OCR processing failure
     */
    public static function prepareOcrFailureEmail($fileName, $projectName, $loginLink, $fileId, $errorMessage = null) {
        // Ensure parameters are not null to avoid deprecated warnings
        $fileName = $fileName ?? 'Document';
        $projectName = $projectName ?? 'Project';
        $loginLink = $loginLink ?? '#';
        $fileId = $fileId ?? 'Unknown';
        
        $subject = "AI Processing Failed - {$fileName} for {$projectName}";
        $errorText = $errorMessage ? " The system reported: " . $errorMessage : '';
        
        return [
            'subject' => $subject,
            'textContent' => "We encountered an issue processing your document '{$fileName}' in project '{$projectName}'{$errorText}.\n\n" .
                           "Document Details:\n" .
                           "• File Name: {$fileName}\n" .
                           "• File ID: {$fileId}\n" .
                           "• Project: {$projectName}\n\n" .
                           "What to do next:\n" .
                           "• Try uploading the document again\n" .
                           "• Ensure the document is a clear, readable bank statement\n" .
                           "• Check that the file is not corrupted or password-protected\n" .
                           "• Contact support if the issue persists\n\n" .
                           "Access your project: {$loginLink}\n\n" .
                           "If you continue to experience issues, please contact our support team and reference the File ID above.\n\n" .
                           "Best regards,\nThe ExactSum Team",
            'htmlContent' => "<!DOCTYPE html>
                            <html lang='en'>
                            <head>
                              <meta charset='UTF-8'>
                              <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                              <title>AI Processing Failed</title>
                            </head>
                            <body style='margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, Helvetica, sans-serif;'>
                              <table cellpadding='0' cellspacing='0' border='0' width='100%' style='background-color: #f4f4f4; margin: 0; padding: 0;'>
                                <tr>
                                  <td align='center' style='padding: 20px;'>
                                    <table cellpadding='0' cellspacing='0' border='0' width='600' style='max-width: 600px; background-color: #ffffff; border-radius: 8px;'>
                                      <tr>
                                        <td style='padding: 30px;'>
                                          <table cellpadding='0' cellspacing='0' border='0' width='100%'>
                                            <!-- Header with warning icon -->
                                            <tr>
                                              <td style='text-align: center; padding-bottom: 20px;'>
                                                <div style='display: inline-block; width: 60px; height: 60px; background-color: #f59e0b; border-radius: 50%; margin-bottom: 15px; position: relative;'>
                                                  <div style='position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 24px; font-weight: bold;'>!</div>
                                                </div>
                                                <h1 style='margin: 0; font-size: 24px; font-weight: bold; color: #333333; font-family: Arial, Helvetica, sans-serif;'>AI Processing Failed</h1>
                                              </td>
                                            </tr>
                                            <!-- Error message -->
                                            <tr>
                                              <td style='text-align: center; padding-bottom: 20px;'>
                                                <p style='margin: 0; font-size: 16px; color: #333333; font-family: Arial, Helvetica, sans-serif; line-height: 1.5;'>We encountered an issue processing your document <span style='color: #4639a1; font-weight: bold;'>" . htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8') . "</span> in project <span style='color: #4639a1; font-weight: bold;'>" . htmlspecialchars($projectName, ENT_QUOTES, 'UTF-8') . "</span>{$errorText}.</p>
                                              </td>
                                            </tr>
                                            <!-- Document details -->
                                            <tr>
                                              <td style='padding: 20px 0;'>
                                                <table cellpadding='0' cellspacing='0' border='0' width='100%'>
                                                  <tr>
                                                    <td style='padding: 15px; background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px;'>
                                                      <h3 style='margin: 0 0 10px 0; font-size: 18px; color: #333333; font-family: Arial, Helvetica, sans-serif;'>Document Details</h3>
                                                      <ul style='margin: 0; padding-left: 20px; font-size: 14px; color: #666666; font-family: Arial, Helvetica, sans-serif; line-height: 1.6;'>
                                                        <li><strong>File Name:</strong> " . htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8') . "</li>
                                                        <li><strong>File ID:</strong> " . htmlspecialchars($fileId, ENT_QUOTES, 'UTF-8') . "</li>
                                                        <li><strong>Project:</strong> " . htmlspecialchars($projectName, ENT_QUOTES, 'UTF-8') . "</li>
                                                      </ul>
                                                    </td>
                                                  </tr>
                                                </table>
                                              </td>
                                            </tr>
                                            <!-- Next steps -->
                                            <tr>
                                              <td style='padding: 20px 0;'>
                                                <table cellpadding='0' cellspacing='0' border='0' width='100%'>
                                                  <tr>
                                                    <td style='padding: 15px; background-color: #e0f2fe; border: 1px solid #81d4fa; border-radius: 8px;'>
                                                      <h3 style='margin: 0 0 10px 0; font-size: 18px; color: #333333; font-family: Arial, Helvetica, sans-serif;'>What to do next</h3>
                                                      <ul style='margin: 0; padding-left: 20px; font-size: 14px; color: #666666; font-family: Arial, Helvetica, sans-serif; line-height: 1.6;'>
                                                        <li>Try uploading the document again</li>
                                                        <li>Ensure the document is a clear, readable bank statement</li>
                                                        <li>Check that the file is not corrupted or password-protected</li>
                                                        <li>Contact support if the issue persists</li>
                                                      </ul>
                                                    </td>
                                                  </tr>
                                                </table>
                                              </td>
                                            </tr>
                                            <!-- CTA Button -->
                                            <tr>
                                              <td style='text-align: center; padding: 20px 0;'>
                                                <table cellpadding='0' cellspacing='0' border='0' style='margin: 0 auto;'>
                                                  <tr>
                                                    <td style='background-color: #4639a1; border-radius: 5px; text-align: center;'>
                                                      <a href='" . htmlspecialchars($loginLink, ENT_QUOTES, 'UTF-8') . "' style='display: inline-block; padding: 12px 24px; color: #ffffff; text-decoration: none; font-size: 16px; font-weight: bold; font-family: Arial, Helvetica, sans-serif;'>Access Your Project</a>
                                                    </td>
                                                  </tr>
                                                </table>
                                              </td>
                                            </tr>
                                            <!-- Support note -->
                                            <tr>
                                              <td style='padding: 20px 0;'>
                                                <table cellpadding='0' cellspacing='0' border='0' width='100%'>
                                                  <tr>
                                                    <td style='padding: 15px; background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px;'>
                                                      <p style='margin: 0; font-size: 14px; color: #856404; font-family: Arial, Helvetica, sans-serif; line-height: 1.5;'>
                                                        <strong>Need Help?</strong> If you continue to experience issues, please contact our support team and reference the <strong>File ID: " . htmlspecialchars($fileId, ENT_QUOTES, 'UTF-8') . "</strong> for faster assistance.
                                                      </p>
                                                    </td>
                                                  </tr>
                                                </table>
                                              </td>
                                            </tr>
                                            <!-- Footer -->
                                            <tr>
                                              <td style='text-align: center; padding-top: 30px; border-top: 1px solid #e9ecef;'>
                                                <p style='margin: 0; font-size: 14px; color: #666666; font-family: Arial, Helvetica, sans-serif;'>Best regards,<br>The ExactSum Team</p>
                                              </td>
                                            </tr>
                                          </table>
                                        </td>
                                      </tr>
                                    </table>
                                  </td>
                                </tr>
                              </table>
                            </body>
                            </html>"
        ];
    }

    /**
     * Prepare email content for user invitation
     */
    public static function prepareUserInvitationEmail($orgName, $inviterEmail, $inviteLink) {
        // Ensure parameters are not null to avoid deprecated warnings
        $orgName = $orgName ?? 'ExactSum';
        $inviterEmail = $inviterEmail ?? 'team@exactsum.com';
        $inviteLink = $inviteLink ?? '#';
        
        return [
            'subject' => 'You\'ve been invited to join ExactSum',
            'textContent' => "You've been invited by " . $inviterEmail . " to join ExactSum, a bank statement analysis service.\n\n" .
                           "Click this link to accept the invitation: " . $inviteLink . "\n\n" .
                           "If the link doesn't work, copy and paste this URL into your browser:\n" . $inviteLink . "\n\n" .
                           "This link will expire in 30 days.",
            'htmlContent' => "<!DOCTYPE html>
                            <html lang='en'>
                            <head>
                              <meta charset='UTF-8'>
                              <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                              <title>ExactSum Invitation</title>
                            </head>
                            <body style='margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, Helvetica, sans-serif;'>
                              <table cellpadding='0' cellspacing='0' border='0' width='100%' style='background-color: #f4f4f4; margin: 0; padding: 0;'>
                                <tr>
                                  <td align='center' style='padding: 20px;'>
                                    <table cellpadding='0' cellspacing='0' border='0' width='600' style='max-width: 600px; background-color: #ffffff; border-radius: 8px;'>
                                      <tr>
                                        <td style='padding: 30px;'>
                                          <table cellpadding='0' cellspacing='0' border='0' width='100%'>
                                            <tr>
                                              <td style='text-align: center; padding-bottom: 20px;'>
                                                <h1 style='margin: 0; font-size: 24px; font-weight: bold; color: #333333; font-family: Arial, Helvetica, sans-serif;'>You've been invited to join <span style='color: #4639a1; font-weight: bold;'>ExactSum</span></h1>
                                              </td>
                                            </tr>
                                            <tr>
                                              <td style='text-align: center; padding-bottom: 20px;'>
                                                <p style='margin: 0; font-size: 16px; color: #333333; font-family: Arial, Helvetica, sans-serif; line-height: 1.5;'>You've been invited by <span style='color: #4639a1; font-weight: bold;'>" . htmlspecialchars($inviterEmail, ENT_QUOTES, 'UTF-8') . "</span> to join ExactSum, a bank statement analysis service.</p>
                                              </td>
                                            </tr>
                                            <tr>
                                              <td style='text-align: center; padding: 20px 0;'>
                                                <table cellpadding='0' cellspacing='0' border='0' style='margin: 0 auto;'>
                                                  <tr>
                                                    <td style='background-color: #4639a1; border-radius: 5px; text-align: center;'>
                                                      <a href='" . htmlspecialchars($inviteLink, ENT_QUOTES, 'UTF-8') . "' style='display: inline-block; padding: 12px 24px; color: #ffffff; text-decoration: none; font-size: 16px; font-weight: bold; font-family: Arial, Helvetica, sans-serif;'>Accept Invitation</a>
                                                    </td>
                                                  </tr>
                                                </table>
                                              </td>
                                            </tr>
                                            <tr>
                                              <td style='text-align: center; padding-top: 20px;'>
                                                <p style='margin: 0 0 10px 0; font-size: 14px; color: #666666; font-family: Arial, Helvetica, sans-serif;'>If the button doesn't work, copy and paste this URL:</p>
                                                <table cellpadding='0' cellspacing='0' border='0' width='100%'>
                                                  <tr>
                                                    <td style='padding: 10px; background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; text-align: center;'>
                                                      <p style='margin: 0; font-family: Courier, monospace; font-size: 12px; color: #495057; word-break: break-all;'>" . htmlspecialchars($inviteLink, ENT_QUOTES, 'UTF-8') . "</p>
                                                    </td>
                                                  </tr>
                                                </table>
                                              </td>
                                            </tr>
                                            <tr>
                                              <td style='text-align: center; padding-top: 20px;'>
                                                <p style='margin: 0; font-size: 14px; color: #666666; font-family: Arial, Helvetica, sans-serif;'>This link will expire in 30 days.</p>
                                              </td>
                                            </tr>
                                          </table>
                                        </td>
                                      </tr>
                                    </table>
                                  </td>
                                </tr>
                              </table>
                            </body>
                            </html>"
        ];
    }

  
  
}
