//
//  EditableCell.h
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 10/21/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import <Foundation/Foundation.h>


@interface EditableCell : UITableViewCell {
	UITextField *_textField;
}
@property (nonatomic, retain) UITextField *textField;

@end
