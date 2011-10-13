//
//  AddNoteViewController.h
//  UITextView
//
//  Created by Ellen Miner on 3/7/09.
//  Copyright 2009 RaddOnline. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "TextViewCell.h"


@interface AddNoteViewController : UITableViewController <UITextViewDelegate> {
	IBOutlet UITableView *tbView;
	NSString *aNote;
}
@property (nonatomic, retain) NSString *aNote;

- (void)save:(id)sender;

@end
